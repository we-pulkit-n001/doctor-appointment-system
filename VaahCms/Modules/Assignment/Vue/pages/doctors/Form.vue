<script setup>
import {onMounted, ref, watch} from "vue";
import { useDoctorStore } from '../../stores/store-doctors'

import VhField from './../../vaahvue/vue-three/primeflex/VhField.vue'
import {useRoute} from 'vue-router';

const store = useDoctorStore();
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
//--------/form_menu

// const isValidTime = (date) => date instanceof Date && !isNaN(date.getTime());

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
                            data-testid="doctors-view_item"
                            @click="store.toView(store.item)"
                            icon="pi pi-eye"/>

                    <Button label="Save"
                            class="p-button-sm"
                            v-if="store.item && store.item.id"
                            data-testid="doctors-save"
                            @click="store.itemAction('save')"
                            icon="pi pi-save"/>

                    <Button label="Create & New"
                            v-else
                            @click="store.itemAction('create-and-new')"
                            class="p-button-sm"
                            data-testid="doctors-create-and-new"
                            icon="pi pi-save"/>


                    <!--form_menu-->
                    <Button
                        type="button"
                        @click="toggleFormMenu"
                        class="p-button-sm"
                        data-testid="doctors-form-menu"
                        icon="pi pi-angle-down"
                        aria-haspopup="true"/>

                    <Menu ref="form_menu"
                          :model="store.form_menu_list"
                          :popup="true" />
                    <!--/form_menu-->


                    <Button class="p-button-primary p-button-sm"
                            icon="pi pi-times"
                            data-testid="doctors-to-list"
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


                <VhField label="Name">
                    <div class="p-inputgroup">
                        <InputText class="w-full"
                                   placeholder="Enter the name"
                                   name="doctors-name"
                                   data-testid="doctors-name"
                                   @update:modelValue="store.watchItem"
                                   v-model="store.item.name" required/>
                        <div class="required-field hidden"></div>
                    </div>
                </VhField>

                <!--adding specialization field-->

                <VhField label="Specialization">
                    <div class="p-inputgroup">
                        <InputText class="w-full"
                                   placeholder="Enter the specialization"
                                   specialization="doctors-specialization"
                                   data-testid="doctors-specialization"
                                   v-model="store.item.specialization" required/>
                        <div class="required-field hidden"></div>
                    </div>
                </VhField>

                <!--adding specialization field-->

                <!--adding email field-->

                <VhField label="Email">
                    <div class="p-inputgroup">
                        <InputText class="w-full"
                                   placeholder="Enter the email"
                                   email="doctors-email"
                                   data-testid="doctors-email"
                                   v-model="store.item.email" required/>
                        <div class="required-field hidden"></div>
                    </div>
                </VhField>

                <!--adding email field-->

                <!--adding phone field-->

                <VhField label="Phone">
                    <div class="p-inputgroup">
                        <InputText class="w-full"
                                   placeholder="Enter the phone"
                                   phone="doctors-phone"
                                   data-testid="doctors-phone"
                                   v-model="store.item.phone" required/>
                        <div class="required-field hidden"></div>
                    </div>
                </VhField>

                <!--adding phone field-->

                <!--adding consultation_fees field-->

                <VhField label="Consultation Fees">
                    <div class="p-inputgroup">
                        <InputNumber class="w-full"
                                     placeholder="Enter the price"
                                     data-testid="appointment-price"
                                     v-model="store.item.price"
                                     mode="decimal"
                                     prefix="$ "
                                     required />
                        <div class="required-field hidden"></div>
                    </div>
                </VhField>

                <!--adding consultation_fees field-->

                <!--adding working_hours_start field-->

                <VhField label="Start Time">
                    <div class="p-inputgroup">
                        <Calendar class="w-full"
                                  v-model="store.item.working_hours_start"
                                  timeOnly
                                  hourFormat="12"
                                  showIcon
                                  placeholder="Select start time"
                                  :stepMinute="30"
                                  data-testid="doctors-working_hours_start">
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

                <!--adding working_hours_start field-->

                <!--adding working_hours_end field-->

                <VhField label="End Time">
                    <div class="p-inputgroup">
                        <Calendar class="w-full"
                                  v-model="store.item.working_hours_end"
                                  timeOnly
                                  hourFormat="12"
                                  showIcon
                                  placeholder="Select end time"
                                  :stepMinute="30"
                                  data-testid="doctors-working_hours_end">
                            <template #inputicon="{ clickCallback }">
                                <i class="pi pi-clock cursor-pointer" @click="clickCallback"></i>
                            </template>
                        </Calendar>
                        <span class="p-inputgroup-addon">
                        <i class="pi pi-clock"></i> <!-- PrimeIcons clock icon -->
                        </span>
                        <div class="required-field hidden"></div>
                    </div>
                </VhField>


                <!--adding working_hours_end field-->

                <!--adding consultation_fees field-->

<!--                <VhField label="Price">-->
<!--                    <div class="p-inputgroup">-->
<!--                        <InputNumber-->
<!--                            class="w-full"-->
<!--                            v-model="store.item.price"-->
<!--                            mode="decimal"-->
<!--                            :min="0"-->
<!--                            step="0.01"-->
<!--                            placeholder="Enter the price"-->
<!--                            :currency="'$'"-->
<!--                            :locale="'en-US'"-->
<!--                            data-testid="price-input"-->
<!--                            required />-->
<!--                        <div class="required-field hidden"></div>-->
<!--                    </div>-->
<!--                </VhField>-->

                <!--adding price field-->

                <!--                <VhField label="Slug">-->
<!--                    <div class="p-inputgroup">-->
<!--                        <InputText class="w-full"-->
<!--                                   placeholder="Enter the slug"-->
<!--                                   name="doctors-slug"-->
<!--                                   data-testid="doctors-slug"-->
<!--                                   v-model="store.item.slug" required/>-->
<!--                        <div class="required-field hidden"></div>-->
<!--                    </div>-->
<!--                </VhField>-->

                <VhField label="Is Active">
                    <InputSwitch v-bind:false-value="0"
                                 v-bind:true-value="1"
                                 class="p-inputswitch-sm"
                                 name="doctors-active"
                                 data-testid="doctors-active"
                                 v-model="store.item.is_active"/>
                </VhField>

            </div>
        </Panel>

    </div>

</template>
