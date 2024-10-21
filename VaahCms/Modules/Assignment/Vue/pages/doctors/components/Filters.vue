<script  setup>

import { useDoctorStore } from '../../../stores/store-doctors'
import VhFieldVertical from './../../../vaahvue/vue-three/primeflex/VhFieldVertical.vue'
import {onBeforeMount} from "vue";
import { ref, computed  } from 'vue';

const store = useDoctorStore();

onBeforeMount(() =>{
    store.getUniqueSpecializations();
})

</script>

<template>
    <div class="col-3" v-if="store.show_filters">

            <Panel class="is-small">

                <template class="p-1" #header>

                    <div class="flex flex-row">
                        <div >
                            <b class="mr-1">Filters</b>
                        </div>

                    </div>

                </template>

                <template #icons>

                    <div class="p-inputgroup">

                        <Button data-testid="doctors-hide-filter"
                                class="p-button-sm"
                                @click="store.show_filters = false">
                            <i class="pi pi-times"></i>
                        </Button>

                    </div>

                </template>

            <VhFieldVertical >
                <template #label>
                    <b>Sort By:</b>
                </template>

                <div class="field-radiobutton">
                    <RadioButton name="sort-none"
                                 inputId="sort-none"
                                 data-testid="doctors-filters-sort-none"
                                 value=""
                                 v-model="store.query.filter.sort" />
                    <label for="sort-none" class="cursor-pointer">None</label>
                </div>
                <div class="field-radiobutton">
                    <RadioButton name="sort-ascending"
                                 inputId="sort-ascending"
                                 data-testid="doctors-filters-sort-ascending"
                                 value="updated_at"
                                 v-model="store.query.filter.sort" />
                    <label for="sort-ascending" class="cursor-pointer">Updated (Ascending)</label>
                </div>
                <div class="field-radiobutton">
                    <RadioButton name="sort-descending"
                                 inputId="sort-descending"
                                 data-testid="doctors-filters-sort-descending"
                                 value="updated_at:desc"
                                 v-model="store.query.filter.sort" />
                    <label for="sort-descending" class="cursor-pointer">Updated (Descending)</label>
                </div>

            </VhFieldVertical>

            <Divider/>

<!--                Test Code-->

                <VhFieldVertical >
                    <template #label>
                        <b>Specialization:</b>
                    </template>
                    <div>
                        <div v-for="(specialization, index) in store.specializations" :key="index" class="field-checkbox">
                            <Checkbox :name="'specialization-' + index"
                                      :inputId="specialization"
                                      :value="specialization"
                                      v-model="store.query.filter.specialization" />
                            <label :for="specialization" class="cursor-pointer">{{ specialization }}</label>
                        </div>
                    </div>
                </VhFieldVertical>

                <Divider/>

                <div>
                    <h4>Select Price Range</h4>
                    <Slider
                        v-model="store.query.filter.price"
                        :range="true"
                        :min="10"
                        :max="500"
                        :step="10"
                        :tooltip="true"
                        tooltipPlacement="top"
                        name="price-all"
                        inputId="price-all"
                        data-testid="doctors-filters-price-all"
                    />
                    <div>
                        Selected Price Range: {{ store.query.filter.price[0] }} - {{ store.query.filter.price[1] }}
                    </div>
                </div>


                <Divider/>
                <div>
                    <h4 class="mb-2">Timings:</h4>
                    <div class="flex space-x-4">
                        <div class="w-full">
                            <h5>Start Time</h5>
                            <Calendar class="w-full"
                                      v-model="store.query.filter.working_hours_start"
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
                        </div>

                        <div class="w-full">
                            <h5>End Time</h5>
                            <Calendar class="w-full"
                                      v-model="store.query.filter.working_hours_end"
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
                        </div>
                    </div>
                </div>



                <Divider/>

<!--                Test Code-->

            <VhFieldVertical >
                <template #label>
                    <b>Is Active:</b>
                </template>

                <div class="field-radiobutton">
                    <RadioButton name="active-all"
                                 inputId="active-all"
                                 value="null"
                                 data-testid="doctors-filters-active-all"
                                 v-model="store.query.filter.is_active" />
                    <label for="active-all" class="cursor-pointer">All</label>
                </div>
                <div class="field-radiobutton">
                    <RadioButton name="active-true"
                                 inputId="active-true"
                                 data-testid="doctors-filters-active-true"
                                 value="true"
                                 v-model="store.query.filter.is_active" />
                    <label for="active-true" class="cursor-pointer">Only Active</label>
                </div>
                <div class="field-radiobutton">
                    <RadioButton name="active-false"
                                 inputId="active-false"
                                 data-testid="doctors-filters-active-false"
                                 value="false"
                                 v-model="store.query.filter.is_active" />
                    <label for="active-false" class="cursor-pointer">Only Inactive</label>
                </div>

            </VhFieldVertical>

             <Divider/>

            <VhFieldVertical >
                <template #label>
                    <b>Trashed:</b>
                </template>

                <div class="field-radiobutton">
                    <RadioButton name="trashed-exclude"
                                 inputId="trashed-exclude"
                                 data-testid="doctors-filters-trashed-exclude"
                                 value=""
                                 v-model="store.query.filter.trashed" />
                    <label for="trashed-exclude" class="cursor-pointer">Exclude Trashed</label>
                </div>
                <div class="field-radiobutton">
                    <RadioButton name="trashed-include"
                                 inputId="trashed-include"
                                 data-testid="doctors-filters-trashed-include"
                                 value="include"
                                 v-model="store.query.filter.trashed" />
                    <label for="trashed-include" class="cursor-pointer">Include Trashed</label>
                </div>
                <div class="field-radiobutton">
                    <RadioButton name="trashed-only"
                                 inputId="trashed-only"
                                 data-testid="doctors-filters-trashed-only"
                                 value="only"
                                 v-model="store.query.filter.trashed" />
                    <label for="trashed-only" class="cursor-pointer">Only Trashed</label>
                </div>

            </VhFieldVertical>


        </Panel>

    </div>
</template>

<style scoped>
.price-range {
    margin-top: 20px;
}
</style>
