<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from 'vue-router';
import { useAppointmentStore } from '../../stores/store-appointments';
import { useRootStore } from '../../stores/root';
import Actions from "./components/Actions.vue";
import Table from "./components/Table.vue";
import Filters from './components/Filters.vue';

const store = useAppointmentStore();
const root = useRootStore();
const route = useRoute();

const exportAppointmentsData = () => {
    store.exportAppointmentsData();
};

onMounted(async () => {
    document.title = 'Appointments - Assignment';
    store.item = null;
    await store.onLoad(route);
    await store.watchRoutes(route);
    await store.watchStates();
    await store.getAssets();
    await store.getList();
    await store.getListCreateMenu();
});

// --------form_menu
const create_menu = ref();
const toggleCreateMenu = (event) => {
    create_menu.value.toggle(event);
};


// test code
const isDialogVisible = ref(false);
const currentStep = ref(0);

const steps = ref([
    { label: 'Upload File' },
    { label: 'Header Mapping' },
    { label: 'Confirm/Fail' }
]);

const uploadedFileName = ref('');

const handleFileUpload = (event) => {
    const file = event.files[0];
    if (file) {
        uploadedFileName.value = file.name;
    } else {
        uploadedFileName.value = '';
    }
};

const nextStep = () => {
    if (currentStep.value < steps.value.length - 1) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 0) {
        currentStep.value--;
    }
};

const finishUpload = () => {
    console.log("Upload process finished");
    closeDialog(); // Close dialog on finish
};

const openDialog = () => {
    isDialogVisible.value = true;
};

const closeDialog = () => {
    isDialogVisible.value = false;
    currentStep.value = 0;
    uploadedFileName.value = '';
};
// test code

</script>

<template>
    <div class="grid" v-if="store.assets">
        <div :class="'col-'+(store.show_filters ? 9 : store.list_view_width)">
            <Panel class="is-small">
                <template class="p-1" #header>
                    <div class="flex flex-row">
                        <div>
                            <b class="mr-1">Appointments</b>
                            <Badge v-if="store.list && store.list.total > 0"
                                   :value="store.list.total">
                            </Badge>
                        </div>
                    </div>
                </template>

                <template #icons>
                    <div class="p-inputgroup">

                        <Button data-testid="appointments-list-import"
                                class="p-button-sm"
                                @click="openDialog">
                            <i class="pi pi-upload mr-1"></i>
                            Import Appointments
                        </Button>

                        <Button data-testid="appointments-list-export"
                                class="p-button-sm"
                                @click="exportAppointmentsData">
                            <i class="pi pi-download mr-1"></i>
                            Export Appointments
                        </Button>

                        <Button data-testid="appointments-list-create"
                                class="p-button-sm"
                                @click="store.toForm()">
                            <i class="pi pi-plus mr-1"></i>
                            Create
                        </Button>

                        <Button data-testid="appointments-list-reload"
                                class="p-button-sm"
                                @click="store.getList()">
                            <i class="pi pi-refresh mr-1"></i>
                        </Button>

                        <Button v-if="root.assets && root.assets.module && root.assets.module.is_dev"
                                type="button"
                                @click="toggleCreateMenu"
                                class="p-button-sm"
                                data-testid="appointments-create-menu"
                                icon="pi pi-angle-down"
                                aria-haspopup="true"/>

                        <Menu ref="create_menu"
                              :model="store.list_create_menu"
                              :popup="true" />
                    </div>
                </template>

                <Actions />
                <Table />
            </Panel>
        </div>

        <Filters />
        <RouterView />

        <Dialog header="Upload CSV File" v-model:visible="isDialogVisible" modal style="width: 50vw" @hide="closeDialog">

            <Steps :model="steps" :activeIndex="currentStep" />

            <div class="content-box">
                <div class="file-upload-container">
                    <span class="file-upload-text">Select a CSV file to import:</span>

                    <FileUpload
                        name="csvFile"
                        accept=".csv"
                        mode="basic"
                        auto
                        chooseLabel="Choose CSV"
                        class="file-upload-button"
                        @upload="handleFileUpload"
                    />
                </div>

                <div v-if="uploadedFileName" class="uploaded-file-name">
                    Uploaded File: {{ uploadedFileName }}
                </div>
            </div>

            <div class="dialog-buttons">
                <Button label="Previous" @click="prevStep" v-if="currentStep > 0" />
                <div class="next-button-container">
                    <Button label="Next" @click="nextStep" v-if="currentStep < steps.length - 1" />
                    <Button label="Finish" @click="finishUpload" v-if="currentStep === steps.length - 1" />
                </div>
            </div>
        </Dialog>
    </div>

</template>


<style scoped>
.file-upload-container {
    display: flex;                  /* Use flexbox for layout */
    justify-content: center;        /* Center the contents horizontally */
    align-items: center;            /* Center vertically */
    height: 100%;                   /* Full height of the dialog */
}

.file-upload-text {
    margin-right: 10px;             /* Space between the text and button */
    flex-shrink: 0;                 /* Prevent the text from shrinking */
}

.file-upload-button {
    flex-shrink: 0;                 /* Prevent the button from shrinking */
}

.uploaded-file-name {
    margin-top: 10px;               /* Space between upload button and file name */
    color: #555;                    /* Optional styling for the file name */
}

.dialog-buttons {
    display: flex;                  /* Use flexbox for layout */
    justify-content: space-between; /* Space out buttons */
    margin-top: 20px;               /* Space between steps and buttons */
}

.next-button-container {
    display: flex;                  /* Use flexbox for layout */
    justify-content: flex-end;      /* Align next/finish button to the right */
    flex-grow: 1;                   /* Allow this container to grow */
}

.content-box {
    border: 1px solid #ccc;         /* Border for the content box */
    border-radius: 8px;             /* Rounded corners */
    padding: 20px;                   /* Padding inside the box */
    margin-top: 20px;                /* Space above the content box */
}

.file-upload-container {
    display: flex;                  /* Use flexbox for layout */
    justify-content: center;        /* Center the contents horizontally */
    align-items: center;            /* Center vertically */
}

.file-upload-text {
    margin-right: 10px;             /* Space between the text and button */
    flex-shrink: 0;                 /* Prevent the text from shrinking */
}

.file-upload-button {
    flex-shrink: 0;                 /* Prevent the button from shrinking */
}

.uploaded-file-name {
    margin-top: 10px;               /* Space between upload button and file name */
    color: #555;                    /* Optional styling for the file name */
    text-align: center;             /* Center the uploaded file name */
}

.dialog-buttons {
    display: flex;                  /* Use flexbox for layout */
    justify-content: space-between; /* Space out buttons */
    margin-top: 20px;               /* Space between steps and buttons */
}

.next-button-container {
    display: flex;                  /* Use flexbox for layout */
    justify-content: flex-end;      /* Align next/finish button to the right */
    flex-grow: 1;                   /* Allow this container to grow */
}

</style>
