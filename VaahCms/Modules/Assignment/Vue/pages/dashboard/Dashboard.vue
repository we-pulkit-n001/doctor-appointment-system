<script  setup>
import {onMounted, ref, watch} from "vue";
import { vaah } from '../../vaahvue/pinia/vaah';
import { useAppointmentStore } from '../../stores/store-appointments'
import {useRoute} from 'vue-router';

const store = useAppointmentStore();
const route = useRoute();

const useVaah = vaah();

onMounted(async () => {
    await store.getDashboardData();
});

document.title = 'Assignment';
</script>

<template>
    <div style="margin-top: 8px; text-align: center; margin-bottom: 80px;">
        <h1 class="text-4xl">Dashboard</h1>
        <div class="container">
            <div class="row">
                <div class="card" style="--color: #333;">
                    <Card>
                        <template #title>
                            <h5>Registered Doctors</h5>
                        </template>
                        <template #icons>
                            <i class="pi pi-user-md"></i>
                        </template>
                        <template #content>
                            <h2 v-if="store.item">
                                {{ store.item.data.registered_doctors }}
                            </h2>
                        </template>
                    </Card>
                </div>
                <div class="card" style="--color: #555;">
                    <Card>
                        <template #title>
                            <h5>Total Appointments</h5>
                        </template>
                        <template #content>
                            <h2 v-if="store.item">
                                {{ store.item.data.total_appointments }}
                            </h2>
                        </template>
                    </Card>
                </div>
                <div class="card" style="--color: #777;">
                    <Card>
                        <template #title>
                            <h5>Cancelled Appointments</h5>
                        </template>
                        <template #content>
                            <h2 v-if="store.item">
                                {{ store.item.data.cancelled_appointments }}
                            </h2>
                        </template>
                    </Card>
                </div>
                <div class="card" style="--color: #999;">
                    <Card>
                        <template #title>
                            <h5>Revenue Till Date</h5>
                        </template>
                        <template #content>
                            <h2 v-if="store.item">
                                {{ store.item.data.revenue }}
                            </h2>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.row {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
}

.card {
    background-color: var(--color);
    flex: 1 1 240px;
    margin: 10px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    padding: 20px;
    color: white;
    transition: transform 0.2s;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 200px;
}

.card:hover {
    transform: translateY(-5px);
}

h5 {
    margin-bottom: 10px;
}
</style>
