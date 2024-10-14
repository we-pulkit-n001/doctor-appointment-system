<script  setup>
import {onMounted, ref, watch} from "vue";
import { useAppointmentStore } from '../../stores/store-appointments'
import {useRoute} from 'vue-router';

// const store = useAppointmentStore();
// const route = useRoute();
//
// const total_appointments = ref(null);
//
// onMounted(async () => {
//     await store.getDashboardStats();
//
//     if (store.item && store.item.data) {
//         total_appointments.value = store.item.data.total_appointments || 0;
//     } else {
//         console.warn("null/undefined");
//     }
// });

document.title = 'Assignment';
</script>
<template>

    <div style="margin-top: 8px; text-align: center; margin-bottom: 80px;">
        <h1 class="text-4xl">Dashboard</h1>

        <!-- Adding Card -->
        <div style="margin-top: 16px; display: flex; justify-content: space-between;">
            <Card style="height: 120px; width: 350px; padding-top: 20px;">
                <template #title>Registered Doctors</template>
                <template #content>
                    <p class="m-0"><b>20</b></p>
                </template>
            </Card>

            <Card style="height: 120px; width: 350px; padding-top: 20px;">
                <template #title>Total Appointments</template>
                <template #content>
                    <p class="m-0"><b>8</b></p>
                </template>
            </Card>

            <Card style="height: 120px; width: 350px; padding-top: 20px;">
                <template #title>Cancelled Appointments</template>
                <template #content>
                    <p class="m-0"><b>1</b></p>
                </template>
            </Card>

            <Card style="height: 120px; width: 350px; padding-top: 20px;">
                <template #title>Revenue Till Date</template>
                <template #content>
                    <p class="m-0"><b>$722</b></p>
                </template>
            </Card>
        </div>
        <!-- Adding Card -->
    </div>

</template>

<style>

</style>
