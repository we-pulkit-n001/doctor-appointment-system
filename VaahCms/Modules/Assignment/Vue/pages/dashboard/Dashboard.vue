<script  setup>
import {onMounted, ref, watch} from "vue";
import { vaah } from '../../vaahvue/pinia/vaah';
import { useAppointmentStore } from '../../stores/store-appointments'
import {useRoute} from 'vue-router';

const store = useAppointmentStore();
const route = useRoute();

const useVaah = vaah();

const chartData = ref();
const chartOptions = ref();

const registered_doctors = ref(null);
const total_appointments = ref(null);
const cancelled_appointments = ref(null);
const revenue_till_date = ref(null);
const total_patients = ref(null);

onMounted(async () => {
    await store.getDashboardData();

        registered_doctors.value = store.item?.data.registered_doctors ?? 0;
        total_appointments.value = store.item?.data.total_appointments ?? 0;
        cancelled_appointments.value = store.item?.data.cancelled_appointments ?? 0;
        revenue_till_date.value = store.item?.data.revenue_till_date ?? 0;
        total_patients.value = store.item?.data.total_patients ?? 0;

    chartData.value = setChartData();
    chartOptions.value = setChartOptions();

});

const setChartData = () => {
    return {
        labels: ['Registered Doctors', 'Overall Patients Registered', 'Appointments Booked', 'Appointments Cancelled'],
        datasets: [
            {
                label: 'Statistics',
                data: [store.item.data.registered_doctors, store.item.data.total_patients, store.item.data.total_appointments, store.item.data.cancelled_appointments],
                backgroundColor: ['rgba(50, 50, 50, 0.5)', 'rgba(150, 150, 150, 0.5)', 'rgba(100, 100, 100, 0.2)', 'rgba(50, 50, 50, 0.4)'],
                borderWidth: 1
            }
        ]
    };
};
const setChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--p-text-color');
    const textColorSecondary = documentStyle.getPropertyValue('--p-text-muted-color');
    const surfaceBorder = documentStyle.getPropertyValue('--p-content-border-color');

    return {
        plugins: {
            legend: {
                labels: {
                    color: textColor
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: textColorSecondary
                },
                grid: {
                    color: surfaceBorder
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    color: textColorSecondary
                },
                grid: {
                    color: surfaceBorder
                }
            }
        }
    };
}

document.title = 'Assignment';
</script>

<template>
    <div class="dashboard">
        <h1 class="text-4xl">Dashboard</h1>
        <div class="container">
            <div class="row">
                <Card class="card">
                    <template #title>
                        <h5>Registered Doctors</h5>
                    </template>
                    <template #content>
                        <div class="content-wrapper">
                            <i class="pi pi-user-md icon"></i>
                            <h2 v-if="store.item">{{ store.item.data.registered_doctors }}</h2>
                        </div>
                    </template>
                </Card>

                <Card class="card">
                    <template #title>
                        <h5>Overall Patients Registered</h5>
                    </template>
                    <template #content>
                        <h2 v-if="store.item">{{ store.item.data.total_patients }}</h2>
                    </template>
                </Card>

                <Card class="card">
                    <template #title>
                        <h5>Appointments Booked</h5>
                    </template>
                    <template #content>
                            <h2 v-if="store.item">{{ store.item.data.total_appointments }}</h2>
                    </template>
                </Card>


                <Card class="card">
                    <template #title>
                        <h5>Appointments Cancelled</h5>
                    </template>
                    <template #content>
                        <h2 v-if="store.item">{{ store.item.data.cancelled_appointments }}</h2>
                    </template>
                </Card>

                <Card class="card">
                    <template #title>
                        <h5>Accumulated Revenue</h5>
                    </template>
                    <template #content>
                        <h2 v-if="store.item">${{ store.item.data.revenue_till_date }}</h2>
                    </template>
                </Card>
            </div>
        </div>

        <!-- Adding Chart view -->
        <div class="chart-card">
            <Card>
                <template #content>
                    <Chart type="bar" :data="chartData" :options="chartOptions" />
                </template>
            </Card>
        </div>
    </div>
</template>

<style scoped>
.dashboard {
    margin: 20px;
    text-align: center;
}

h1 {
    margin-bottom: 10px;
    margin-top: 0;
}

.container {
    display: flex;
    flex-direction: column;
}

.row {
    display: flex;
    justify-content: space-between;
}

.card {
    flex: 1;
    margin: 10px;
    background-color: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.card:hover {
    transform: translateY(-2px);
}

.content-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon {
    font-size: 24px;
    margin-right: 10px;
}

.chart-card {
    margin-top: 20px;
    background-color: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.chart-card canvas {
    height: 100% !important;
    width: auto !important;
}


</style>
