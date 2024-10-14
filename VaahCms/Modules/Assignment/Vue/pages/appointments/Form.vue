<script setup>
import {onMounted, ref, watch, computed} from "vue";
import { useAppointmentStore } from '../../stores/store-appointments'

import VhField from './../../vaahvue/vue-three/primeflex/VhField.vue'
import {useRoute} from 'vue-router';


const store = useAppointmentStore();
const route = useRoute();

onMounted(async () => {
    /**
     * Fetch the record from the database
     */
    if((!store.item || Object.keys(store.item).length < 1)
            && route.params && route.params.id)
    {
        await store.getItem(route.params.id);
    }

    await store.getFormMenu();
});

//--------form_menu
const form_menu = ref();
const toggleFormMenu = (event) => {
    form_menu.value.toggle(event);
};



const appointment_time= ref(null);

const status = ref('');

const statusOptions = [
    { label: 'Book Appointment', value: 'booked' },
    { label: 'Cancel Appointment', value: 'cancelled' },
];

const filteredStatusOptions = computed(() => {
    if (status.value === 'booked') {
        return statusOptions.filter(option => option.value === 'cancelled');
    } else if (status.value === 'cancelled') {
        return statusOptions.filter(option => option.value === 'booked');
    }

    return statusOptions;
});

const minuteTemplate = (minute) => {
    return minute === 0 || minute === 30;
};

const consultationFees = ref('');

const updateConsultationFees = () => {
    const selectedDoctor = store.assets.doctor.find(doctor => doctor.id === store.item.doctor_id);
    consultationFees.value = selectedDoctor ? selectedDoctor.consultation_fees : '';
};


//--------/form_menu

</script>
<template>

    <div class="col-6" >

        <Panel class="is-small">

            <template class="p-1" #header>


                <div class="flex flex-row">
                    <div class="p-panel-title">
                        <span v-if="store.item && store.item.id">
                            Update
                        </span>
                        <span v-else>
                            Create
                        </span>
                    </div>

                </div>


            </template>

            <template #icons>


                <div class="p-inputgroup">

                    <Button class="p-button-sm"
                            v-tooltip.left="'View'"
                            v-if="store.item && store.item.id"
                            data-testid="appointments-view_item"
                            @click="store.toView(store.item)"
                            icon="pi pi-eye"/>

                    <Button label="Save"
                            class="p-button-sm"
                            v-if="store.item && store.item.id"
                            data-testid="appointments-save"
                            @click="store.itemAction('save')"
                            icon="pi pi-save"/>

                    <Button label="Create & New"
                            v-else
                            @click="store.itemAction('create-and-new')"
                            class="p-button-sm"
                            data-testid="appointments-create-and-new"
                            icon="pi pi-save"/>


                    <!--form_menu-->
                    <Button
                        type="button"
                        @click="toggleFormMenu"
                        class="p-button-sm"
                        data-testid="appointments-form-menu"
                        icon="pi pi-angle-down"
                        aria-haspopup="true"/>

                    <Menu ref="form_menu"
                          :model="store.form_menu_list"
                          :popup="true" />
                    <!--/form_menu-->


                    <Button class="p-button-primary p-button-sm"
                            icon="pi pi-times"
                            data-testid="appointments-to-list"
                            @click="store.toList()">
                    </Button>
                </div>



            </template>


            <div v-if="store.item" class="mt-2">

                <Message severity="error"
                         class="p-container-message mb-3"
                         :closable="false"
                         icon="pi pi-trash"
                         v-if="store.item.deleted_at">

                    <div class="flex align-items-center justify-content-between">

                        <div class="">
                            Deleted {{store.item.deleted_at}}
                        </div>

                        <div class="ml-3">
                            <Button label="Restore"
                                    class="p-button-sm"
                                    data-testid="articles-item-restore"
                                    @click="store.itemAction('restore')">
                            </Button>
                        </div>

                    </div>

                </Message>


                <VhField label="Patient's Name">
                    <div class="p-inputgroup">
                        <Dropdown  v-model="store.item.patient_id"
                                   :options="store.assets.patient"
                                   placeholder="Select Patient Name"
                                   option-label="name"
                                   option-value="id"
                                   name="patient-name"
                                   data-testid="patient-name"
                                   filter
                                   required/>
                        <div class="required-field hidden"></div>
                    </div>
                </VhField>

                <VhField label="Doctor's Name">
                    <div class="p-inputgroup">
                        <Dropdown  v-model="store.item.doctor_id"
                                   :options="store.assets.doctor"
                                   placeholder="Select Doctor Name"
                                   option-label="name"
                                   option-value="id"
                                   name="doctor-name"
                                   data-testid="doctor-name"
                                   filter
                                   required
                                   @change="updateConsultationFees"/>
                        <div class="required-field hidden"></div>
                    </div>
                </VhField>

                <!--adding consultation_fees field-->

                <VhField label="Consultation Fee">
                    <div class="p-inputgroup">
                        <span class="p-inputgroup-addon">$</span>
                        <InputText class="w-full"
                                   placeholder="Select Doctor for Fee"
                                   name="consultation-fees"
                                   data-testid="consultation-fees"
                                   v-model="consultationFees"
                                   required
                                   readonly/>
                        <div class="required-field hidden"></div>
                    </div>
                </VhField>

<!--                <VhField label="Consultation Fees">-->
<!--                    <div class="p-inputgroup">-->
<!--                        <InputNumber class="w-full"-->
<!--                                     placeholder="Enter the price"-->
<!--                                     data-testid="appointment-price"-->
<!--                                     v-model="store.item.consultation_fees"-->
<!--                                     mode="decimal"-->
<!--                                     prefix="$ "-->
<!--                                     required />-->
<!--                        <div class="required-field hidden"></div>-->
<!--                    </div>-->
<!--                </VhField>-->

                <!--adding consultation_fees field-->

                <!--adding date field-->

                <VhField label="Date">
                    <div class="p-inputgroup">
                        <Calendar
                            class="w-full"
                            v-model="store.item.date"
                            placeholder="Select date"
                            data-testid="appointments-date"
                            required
                            :showIcon="true"
                            dateFormat="dd/mm/yy"
                            :minDate="new Date()"
                        />
                        <div class="required-field hidden"></div>
                    </div>
                </VhField>


                <!--adding date field-->

                <!--adding time field-->

                    <VhField label="Time">
                        <div class="p-inputgroup">
                            <Calendar class="w-full"
                                      v-model="store.item.time"
                                      timeOnly
                                      hourFormat="12"
                                      showIcon
                                      placeholder="Select time"
                                      :stepMinute="30"
                                      :minuteTemplate="minuteTemplate">
                                <template #inputicon="{ clickCallback }">
                                    <i class="pi pi-clock cursor-pointer" @click="clickCallback"></i>
                                </template>
                            </Calendar>
                            <span class="p-inputgroup-addon">
                <i class="pi pi-clock"></i>
            </span>
                            <div class="required-field hidden"></div>
                        </div>
                    </VhField>

                <!--adding time field-->

                <!--adding status field-->

<!--                <VhField label="Slug">-->
<!--                    <div class="p-inputgroup">-->
<!--                        <InputText class="w-full"-->
<!--                                   placeholder="Enter the slug"-->
<!--                                   name="appointments-slug"-->
<!--                                   data-testid="appointments-slug"-->
<!--                                   v-model="store.item.slug" required/>-->
<!--                        <div class="required-field hidden"></div>-->
<!--                    </div>-->
<!--                </VhField>-->

<!--                <VhField label="Is Active">-->
<!--                    <InputSwitch v-bind:false-value="0"-->
<!--                                 v-bind:true-value="1"-->
<!--                                 class="p-inputswitch-sm"-->
<!--                                 name="appointments-active"-->
<!--                                 data-testid="appointments-active"-->
<!--                                 v-model="store.item.is_active"/>-->
<!--                </VhField>-->

            </div>
        </Panel>

    </div>

</template>
