<script setup>
import {onMounted, reactive, ref} from "vue";
import {useRoute} from 'vue-router';

import {useDoctorStore} from '../../stores/store-doctors'
import {useRootStore} from '../../stores/root'

import Actions from "./components/Actions.vue";
import Table from "./components/Table.vue";
import Filters from './components/Filters.vue'

const store = useDoctorStore();
const root = useRootStore();
const route = useRoute();

import { useConfirm } from "primevue/useconfirm";
const confirm = useConfirm();


onMounted(async () => {
    document.title = 'Doctors - Assignment';
    store.item = null;
    /**
     * call onLoad action when List view loads
     */
    await store.onLoad(route);

    /**
     * watch routes to update view, column width
     * and get new item when routes get changed
     */
    await store.watchRoutes(route);

    /**
     * watch states like `query.filter` to
     * call specific actions if a state gets
     * changed
     */
    await store.watchStates();

    /**
     * fetch assets required for the crud
     * operation
     */
    await store.getAssets();

    /**
     * fetch list of records
     */
    await store.getList();

    await store.getListCreateMenu();

});

//--------form_menu
const create_menu = ref();
const toggleCreateMenu = (event) => {
    create_menu.value.toggle(event);
};
//--------/form_menu

const exportDoctorData = () => {
    store.exportDoctorData();
}

const fileInput = ref(null);

const importDoctorsData = (data) => {
    store.importDoctorsData(data);
};

const handleFileChange = async (event) => {
    const file = event.target.files[0];
    if (file) {
        const text = await file.text();
        const parsedData = parseCSV(text);
        importDoctorsData(parsedData);
    }
};

const triggerFileInput = () => {
    fileInput.value.click();
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

</script>
<template>

    <div class="grid" v-if="store.assets">

        <div :class="'col-'+(store.show_filters?9:store.list_view_width)">
            <Panel class="is-small">

                <template class="p-1" #header>

                    <div class="flex flex-row">
                        <div >
                            <b class="mr-1">Doctors</b>
                            <Badge v-if="store.list && store.list.total > 0"
                                   :value="store.list.total">
                            </Badge>
                        </div>

                    </div>

                </template>

                <template #icons>

                    <div class="p-inputgroup">

                        <input
                            type="file"
                            accept=".csv"
                            ref="fileInput"
                            @change="handleFileChange"
                            style="display: none"
                        />
                        <Button
                            data-testid="doctors-list-import"
                            class="p-button-sm"
                            @click="triggerFileInput"
                        >
                            <i class="pi pi-upload mr-1"></i>
                            Import Doctors
                        </Button>

                        <Button data-testid="doctors-list-export"
                                class="p-button-sm"
                                @click="exportDoctorData">
                            <i class="pi pi-download mr-1"></i>
                            Export Doctors
                        </Button>

                    <Button data-testid="doctors-list-create"
                            class="p-button-sm"
                            @click="store.toForm()">
                        <i class="pi pi-plus mr-1"></i>
                        Create
                    </Button>

                    <Button data-testid="doctors-list-reload"
                            class="p-button-sm"
                            @click="store.getList()">
                        <i class="pi pi-refresh mr-1"></i>
                    </Button>

                    <!--form_menu-->

                    <Button v-if="root.assets && root.assets.module
                                                && root.assets.module.is_dev"
                        type="button"
                        @click="toggleCreateMenu"
                        class="p-button-sm"
                        data-testid="doctors-create-menu"
                        icon="pi pi-angle-down"
                        aria-haspopup="true"/>

                    <Menu ref="create_menu"
                          :model="store.list_create_menu"
                          :popup="true" />

                    <!--/form_menu-->

                    </div>

                </template>

                <Actions/>

                <Table/>

            </Panel>
        </div>

         <Filters/>

        <RouterView/>

    </div>


</template>
