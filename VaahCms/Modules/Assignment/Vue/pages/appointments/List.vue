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
const active = ref(0);
const uploadedFileName = ref('');
const fileUploaded = ref(false);
const headers = ref([]);
const headerMappings = ref({
    patientEmail: '',
    doctorEmail: '',
    time: '',
    status: ''
});

const items = ref([
    { label: 'Upload' },
    { label: 'Mapping' },
    { label: 'Preview' }
]);

const importAppointmentsData = (data) => {
    console.log("Data to import:", data);
};

const handleFileUpload = async (event) => {
    const file = event.files[0];
    if (file) {
        uploadedFileName.value = file.name;
        fileUploaded.value = true;
        const text = await file.text();
        const parsedData = parseCSV(text);
        headers.value = Object.keys(parsedData[0]);
        importAppointmentsData(parsedData);
    } else {
        uploadedFileName.value = '';
        fileUploaded.value = false;
    }
};

const parseCSV = (csv) => {
    const rows = csv.split('\n').map(row => row.split(','));
    const headers = rows[0].map(header => header.replace(/"/g, '').trim().toLowerCase());

    return rows.slice(1).map(row => {
        return headers.reduce((acc, header, index) => {
            acc[header] = row[index]?.replace(/"/g, '').trim() || '';
            return acc;
        }, {});
    });
};

const nextStep = () => {
    if (active.value === 0) {
        if (!fileUploaded.value) {
            alert("Please upload a file before proceeding to the next step.");
            return;
        }
    }
    if (active.value === 1) {
        if (!Object.values(headerMappings.value).every(value => value)) {
            alert("Please map all fields before proceeding to the next step.");
            return;
        }
    }
    if (active.value < items.value.length - 1) {
        active.value++;
    }
};

const prevStep = () => {
    if (active.value > 0) {
        active.value--;
    }
};

const finishUpload = () => {
    console.log("Upload process finished with mappings:", headerMappings.value);
    closeDialog();
};

const openDialog = () => {
    isDialogVisible.value = true;
    active.value = 0;
};

const closeDialog = () => {
    isDialogVisible.value = false;
    active.value = 0;
    uploadedFileName.value = '';
    fileUploaded.value = false;
    headers.value = [];
    Object.keys(headerMappings.value).forEach(key => headerMappings.value[key] = '');
};

onMounted(async () => {
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
            <div class="card">
                <div class="flex mb-2 gap-2 justify-content-end">
                    <Button @click="active = 0" rounded label="1" class="w-2rem h-2rem p-0" :outlined="active !== 0" />
                    <Button @click="active = 1" rounded label="2" class="w-2rem h-2rem p-0" :outlined="active !== 1" />
                    <Button @click="active = 2" rounded label="3" class="w-2rem h-2rem p-0" :outlined="active !== 2" />
                </div>
                <Steps v-model:activeStep="active" :model="items" />
            </div>

            <div class="content-box">
                <template v-if="active === 0">
                    <div class="file-upload-row">
                        <div class="file-upload-container">
                            <span class="file-upload-text">Select a CSV file to Import:</span>
                            <FileUpload
                                name="csvFile"
                                accept=".csv"
                                mode="basic"
                                auto
                                chooseLabel="Click to Upload"
                                class="file-upload-button"
                                @select="handleFileUpload"
                            />
                        </div>
                    </div>
                    <div v-if="uploadedFileName" class="uploaded-file-name">
                        Uploaded File: {{ uploadedFileName }}
                    </div>
                </template>

                <template v-if="active === 1">
                    <div>
                        <h3>Mapping Step</h3>
                        <p>Please map the following fields from the uploaded CSV file:</p>
                        <div v-for="(field, index) in ['patientEmail', 'doctorEmail', 'time', 'status']" :key="index" class="mapping-row">
                            <div class="mapping-label">
                                <label :for="field">{{ field.replace(/([A-Z])/g, ' $1') }}:</label>
                            </div>
                            <div class="mapping-dropdown">
                                <Dropdown
                                    :options="headers.map(header => ({ label: header, value: header }))"
                                    v-model="headerMappings[field]"
                                    placeholder="Select a header"
                                    optionLabel="label"
                                />
                            </div>
                        </div>
                    </div>
                </template>

                <template v-if="active === 2">
                    <div>
                        <h3>Preview Step</h3>
                        <p>Display a preview of the appointments to be imported.</p>
                        <table>
                            <thead>
                            <tr>
                                <th>Doctor Email</th>
                                <th>Patient Email</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>{{ headerMappings.doctorEmail }}</td>
                                <td>{{ headerMappings.patientEmail }}</td>
                                <td>{{ headerMappings.time }}</td>
                                <td>{{ headerMappings.status }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>

            <div class="dialog-buttons">
                <Button label="Previous" @click="prevStep" v-if="active > 0" />
                <div class="next-button-container">
                    <Button label="Next" @click="nextStep" v-if="active < items.length - 1" />
                    <Button label="Finish" @click="finishUpload" v-if="active === items.length - 1" />
                </div>
            </div>
        </Dialog>
    </div>
</template>

<style scoped>
.card {
    padding: 20px;
}

.mapping-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
}

.mapping-label {
    flex-basis: 40%;
    text-align: left;
    margin-right: 10px;
}

.mapping-dropdown {
    flex-basis: 50%;
}

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

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

th {
    background-color: #f4f4f4;
}
</style>
