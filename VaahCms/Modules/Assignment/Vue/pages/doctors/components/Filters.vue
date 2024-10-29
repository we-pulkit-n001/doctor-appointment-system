<script  setup>

import { useDoctorStore } from '../../../stores/store-doctors'
import VhFieldVertical from './../../../vaahvue/vue-three/primeflex/VhFieldVertical.vue'
import {onBeforeMount} from "vue";
import { ref, computed  } from 'vue';

const store = useDoctorStore();

const selectedTimings = ref([]);

const timingIntervals = ref([
    { label: '12am - 3am', value: '12am-3am' },
    { label: '3am - 6am', value: '3am-6am' },
    { label: '6am - 9am', value: '6am-9am' },
    { label: '9am - 12pm', value: '9am-12pm' },
    { label: '12pm - 3pm', value: '12pm-3pm' },
    { label: '3pm - 6pm', value: '3pm-6pm' },
    { label: '6pm - 9pm', value: '6pm-9pm' },
    { label: '9pm - 12am', value: '9pm-12am' }
]);

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
                        <div v-for="(specialization_count, specialization) in store.specializations" :key="specialization" class="field-checkbox">
                            <Checkbox :name="specialization"
                                      :inputId="specialization"
                                      :value="specialization"
                                      v-model="store.query.filter.specialization" />
                            <label :for="specialization" class="cursor-pointer">{{ specialization }} ({{specialization_count}})</label>
                        </div>
                    </div>
                </VhFieldVertical>


                <Divider/>

                <div>
                    <h4>Select Price Range</h4>
                    <Slider
                        v-model="store.query.filter.price_range"
                        :range="true"
                        :min="10"
                        :max="store.max_price_display"
                        :step="10"
                        :tooltip="true"
                        tooltipPlacement="top"
                        name="price-all"
                        inputId="price-all"
                        data-testid="doctors-filters-price-all"
                    />
                    <div>
                        Selected Price Range: ${{ store.query.filter.price_range[0] }} - ${{ store.query.filter.price_range[1] }}
                    </div>
                </div>


                <Divider/>

                <VhFieldVertical>
                    <template #label>
                        <b>Timings:</b>
                    </template>
                    <div>
                        <div v-for="(interval, index) in store.$timings_display" :key="index" class="field-checkbox">
                            <Checkbox
                                :name="`timings_${index}`"
                                :inputId="`timings_${index}`"
                                :value="interval"
                                v-model="store.query.filter.timings"
                            />
                            <label :for="`timings_${index}`" class="cursor-pointer">{{ interval }}</label>
                        </div>
                    </div>
                </VhFieldVertical>

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
