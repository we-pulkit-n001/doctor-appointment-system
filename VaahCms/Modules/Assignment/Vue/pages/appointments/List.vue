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

const isDialogVisible = ref(false);
const currentStep = ref(0);
const steps = ref([
    { label: 'Upload' },
    { label: 'Map' },
    { label: 'Preview' },
    { label: 'Result' }
]);

const uploadedFileName = ref('');

const handleFileUpload = (event) => {
    console.log('here');
    // const file = event.files[0];
    // if (file) {
    //     uploadedFileName.value = file.name;
    // } else {
    //     uploadedFileName.value = '';
    // }
};

const nextStep = async () => {
    if (currentStep.value === 0 && uploadedFileName.value) {
        // If on the Upload step, send the file to the store
        const file = event.files[0]; // Get the file for uploading
        await store.importAppointments(file); // Call the store's import method
    }
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
    isDialogVisible.value = true; // Open dialog
    currentStep.value = 0; // Reset to first step
};

const closeDialog = () => {
    isDialogVisible.value = false;
    currentStep.value = 0;
    uploadedFileName.value = '';
    store.clearMessages(); // Clear previous messages in store
};

onMounted(async () => {
    // Load initial data when the component is mounted
    document.title = 'Appointments - Assignment';
    await store.onLoad(route);
    await store.watchRoutes(route);
    await store.watchStates();
    await store.getAssets();
    await store.getList();
    await store.getListCreateMenu();
});
</script>

<template>
    <div class="grid" v-if="store.assets">
        <div :class="'col-' + (store.show_filters ? 9 : store.list_view_width)">
            <Panel class="is-small">
                <template #header>
                    <div class="flex flex-row">
                        <div>
                            <b class="mr-1">Appointments</b>
                            <Badge v-if="store.list && store.list.total > 0" :value="store.list.total" />
                        </div>
                    </div>
                </template>

                <template #icons>
                    <div class="p-inputgroup">
                        <Button data-testid="appointments-list-import" class="p-button-sm" @click="openDialog">
                            <i class="pi pi-upload mr-1"></i>
                            Import Appointments
                        </Button>
                        <Button data-testid="appointments-list-export" class="p-button-sm" @click="exportAppointmentsData">
                            <i class="pi pi-download mr-1"></i>
                            Export Appointments
                        </Button>
                        <Button data-testid="appointments-list-create" class="p-button-sm" @click="store.toForm()">
                            <i class="pi pi-plus mr-1"></i>
                            Create
                        </Button>
                        <Button data-testid="appointments-list-reload" class="p-button-sm" @click="store.getList()">
                            <i class="pi pi-refresh mr-1"></i>
                        </Button>
                        <Button
                            v-if="root.assets && root.assets.module && root.assets.module.is_dev"
                            type="button"
                            @click="toggleCreateMenu"
                            class="p-button-sm"
                            data-testid="appointments-create-menu"
                            icon="pi pi-angle-down"
                            aria-haspopup="true"
                        />
                        <Menu ref="create_menu" :model="store.list_create_menu" :popup="true" />
                    </div>
                </template>

                <Actions />
                <Table />
            </Panel>
        </div>

        <Filters />
        <RouterView />

        <Dialog header="Bulk Import" v-model:visible="isDialogVisible" modal style="width: 50vw" @hide="closeDialog">
            <Steps :model="steps" :activeIndex="currentStep" />

            <div class="content-box">
                <template v-if="currentStep === 0">
                    <div class="file-upload-row">
                        <div class="file-upload-container">
                            <span class="file-upload-text">Select a CSV file to Import </span>
                            <FileUpload
                                name="csvFile"
                                accept=".csv"
                                mode="basic"
                                auto
                                chooseLabel="Click to Upload"
                                class="file-upload-button"
                                @upload="handleFileUpload"
                            />
                        </div>
                    </div>
                    <div v-if="uploadedFileName" class="uploaded-file-name">
                        Uploaded File: {{ uploadedFileName }}
                    </div>
                </template>

                <template v-else-if="currentStep === 1">
                    <div>
                        <!-- Display content related to mapping here -->
                        <h3>Mapping Step</h3>
                        <p>Provide mapping instructions or options for the uploaded CSV data.</p>
                    </div>
                </template>

                <template v-else-if="currentStep === 2">
                    <div>
                        <!-- Display a preview of the data here -->
                        <h3>Preview Step</h3>
                        <p>Display a preview of the appointments to be imported.</p>
                        <!-- You could map through data to display it here -->
                    </div>
                </template>

                <template v-else-if="currentStep === 3">
                    <div>
                        <!-- Display result after upload here -->
                        <h3>Result Step</h3>
                        <p>Display the result of the import process.</p>
                        <div v-if="store.uploadSuccess" class="upload-success">{{ store.uploadSuccess }}</div>
                        <div v-if="store.uploadError" class="upload-error">{{ store.uploadError }}</div>
                    </div>
                </template>
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
    display: flex;
    align-items: center;
}

.file-upload-row {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 20px;
}

.file-upload-text {
    margin-right: 10px;
    flex-shrink: 0;
}

.file-upload-button {
    margin-right: 10px;
}

.uploaded-file-name {
    margin-top: 10px;
    color: #555;
    text-align: center;
}

.dialog-buttons {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.next-button-container {
    display: flex;
    justify-content: flex-end;
    flex-grow: 1;
}

.content-box {
    border: 1px dashed #ccc;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
}
</style>

