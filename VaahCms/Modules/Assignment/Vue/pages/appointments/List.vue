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
const parsedData = ref([]);
const validationResult = ref({ isValid: false, message: '' });

const items = ref([
    { label: 'Upload' },
    { label: 'Mapping' },
    { label: 'Preview' },
    { label: 'Result & Validation' }  // Step 4
]);

const importAppointmentsData = (data) => {
    parsedData.value = data; // Store parsed data for later
    console.log(parsedData.value);
};

const handleFileUpload = async (event) => {
    const file = event.files[0];
    if (file) {
        uploadedFileName.value = file.name;
        fileUploaded.value = true;
        const text = await file.text();
        const parsedDataArray = parseCSV(text);
        headers.value = Object.keys(parsedDataArray[0]);
        importAppointmentsData(parsedDataArray);
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
    if (active.value === 0 && !fileUploaded.value) {
        alert("Please upload a file before proceeding.");
        return;
    }
    if (active.value === 1 && !Object.values(headerMappings.value).every(value => value)) {
        alert("Please map all fields before proceeding.");
        return;
    }

    // Generate and log JSON when clicking "Next" after preview (Step 2).
    if (active.value === 2) {
        const jsonData = generateMappedJson();
        // You can store this jsonData in a variable or pass it to the backend
        store.importAppointmentsData(jsonData);
    }

    if (active.value < items.value.length - 1) {
        active.value++;
    }

    if (active.value === 3) {
        validateData();
    }
};

const prevStep = () => {
    if (active.value > 0) {
        active.value--;
    }
};

const finishUpload = () => {
    console.log("Upload completed with mappings:", headerMappings.value);
    closeDialog();
};

const validateData = () => {
    // Basic validation, you can add more advanced validation here
    validationResult.value = { isValid: true, message: "All records are valid!" };

    // Example of failing validation:
    if (parsedData.value.some(row => !row[headerMappings.value.patientEmail] || !row[headerMappings.value.doctorEmail])) {
        validationResult.value = {
            isValid: false,
            message: "Some records are missing important fields."
        };
    }
};

const generateMappedJson = () => {
    const mappedJson = parsedData.value.map(row => ({
        "patient_email": row[headerMappings.value.patientEmail.value],
        "doctor_email": row[headerMappings.value.doctorEmail.value],
        "time": row[headerMappings.value.time.value],
        "status": row[headerMappings.value.status.value]
    }));

    console.log("Generated JSON Data: ", mappedJson);
    return mappedJson;
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
    parsedData.value = []; // Clear parsed data on close
    Object.keys(headerMappings.value).forEach(key => headerMappings.value[key] = '');
    validationResult.value = { isValid: false, message: '' };
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
                <Steps v-model:activeStep="active" :model="items" />

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
                </template>

                <template v-if="active === 2">
                    <h3>Preview Step</h3>
                    <p>Here is a preview of the appointments to be imported:</p>
                    <table>
                        <thead>
                        <tr>
                            <th>Patient Email</th>
                            <th>Doctor Email</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(row, index) in parsedData" :key="index">
                            <td>{{ row[headerMappings.patientEmail.value] }}</td>
                            <td>{{ row[headerMappings.doctorEmail.value] }}</td>
                            <td>{{ row[headerMappings.time.value] }}</td>
                            <td>{{ row[headerMappings.status.value] }}</td>
                        </tr>
                        </tbody>
                    </table>
                </template>


                <template v-if="active === 3">
                    <h3>Result & Validation Step</h3>
                    <p>Validation Results:</p>
                    <div v-if="validationResult.isValid">
                        <p class="valid-message">{{ validationResult.message }}</p>
                    </div>
                    <div v-else>
                        <p class="invalid-message">{{ validationResult.message }}</p>
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

.valid-message {
    color: green;
}

.invalid-message {
    color: red;
}
</style>
