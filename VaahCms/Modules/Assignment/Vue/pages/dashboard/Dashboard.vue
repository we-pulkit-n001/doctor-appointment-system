<script setup>
import { onMounted, ref } from "vue";
import { vaah } from '../../vaahvue/pinia/vaah';
import { useAppointmentStore } from '../../stores/store-appointments';
import { useRoute } from 'vue-router';

const store = useAppointmentStore();
const route = useRoute();

const useVaah = vaah();

const chartData = ref();
const chartOptions = ref();
const doughnutChartData = ref();
const doughnutChartOptions = ref();

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

    doughnutChartData.value = setDoughnutChartData();
    doughnutChartOptions.value = setDoughnutChartOptions();
});

const setChartData = () => {
    return {
        labels: ['Registered Doctors', 'Overall Patients Registered'],
        datasets: [
            {
                label: 'Statistics',
                data: [
                    store.item.data.registered_doctors,
                    store.item.data.total_patients,
                ],
                backgroundColor: [
                    'rgba(50, 50, 50, 0.5)',
                    'rgba(150, 150, 150, 0.5)',
                ],
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
        responsive: true,
        maintainAspectRatio: false,
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
};

const setDoughnutChartData = () => {
    return {
        labels: ['Appointments Booked', 'Appointments Cancelled'],
        datasets: [
            {
                data: [
                    store.item.data.total_appointments,
                    store.item.data.cancelled_appointments
                ],
                backgroundColor: [
                    'rgba(100, 100, 100, 0.2)',
                    'rgba(50, 50, 50, 0.4)'
                ],
                hoverBackgroundColor: [
                    'rgba(100, 100, 100, 0.4)',
                    'rgba(50, 50, 50, 0.6)'
                ]
            }
        ]
    };
};

const setDoughnutChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--text-color');

    return {
        plugins: {
            legend: {
                labels: {
                    cutout: '60%',
                    color: textColor
                }
            }
        }
    };
};

document.title = 'Assignment';
</script>

<template>
    <div class="dashboard">
        <h1 class="text-4xl">Dashboard</h1>
        <div class="container">
            <div class="row">
                <Card class="card">
                    <template #title>
                        <div class="card-title">
                            <i class="pi pi-user icon"></i>
                        </div>
                        <h5>Registered Doctors</h5>
                    </template>
                    <template #content>
                        <div class="content-wrapper">
                            <h2 v-if="store.item">{{ store.item.data.registered_doctors }}</h2>
                        </div>
                    </template>
                </Card>

                <Card class="card">
                    <template #title>
                        <div class="card-title">
                            <i class="pi pi-users icon"></i>
                        </div>
                        <h5>Overall Patients Registered</h5>
                    </template>
                    <template #content>
                        <h2 v-if="store.item">{{ store.item.data.total_patients }}</h2>
                    </template>
                </Card>





                <Card class="card">
                    <template #title>
                        <div class="card-title">
                            <i class="pi pi-calendar-plus icon"></i>
                        </div>
                        <h5>Appointments Booked</h5>
                    </template>
                    <template #content>
                        <h2 v-if="store.item">{{ store.item.data.total_appointments }}</h2>
                    </template>
                </Card>

                <Card class="card">
                    <template #title>
                        <div class="card-title">
                            <i class="pi pi-calendar-times icon"></i>
                        </div>
                        <h5>Appointments Cancelled</h5>
                    </template>
                    <template #content>
                        <h2 v-if="store.item">{{ store.item.data.cancelled_appointments }}</h2>
                    </template>
                </Card>

                <Card class="card">
                    <template #title>
                        <div class="card-title">
                            <i class="pi pi-dollar icon"></i>
                        </div>
                        <h5>Accumulated Revenue</h5>
                    </template>
                    <template #content>
                        <h2 v-if="store.item">${{ store.item.data.revenue_till_date }}</h2>
                    </template>
                </Card>
            </div>
        </div>

        <!-- Modified Charts Container -->
        <div class="charts-container">
            <div class="chart-card bar-chart-card">
                <div class="bar-chart">
                    <Chart type="bar" :data="chartData" :options="chartOptions" />
                </div>
            </div>

            <div class="chart-card doughnut-chart-card">
                <div class="doughnut-chart">
                    <Chart type="doughnut" :data="doughnutChartData" :options="doughnutChartOptions" />
                </div>
            </div>
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

/* Modify the heading style for card titles */
.card h5 {
    font-weight: normal; /* Change to normal weight for less boldness */
    margin: 0; /* Remove margin for better alignment */
}

/* Modified Charts Container Styles */
.charts-container {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
    flex-wrap: wrap;
}

.chart-card {
    width: 48%;
    max-width: 100%;
    background-color: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    height: 350px;
}

.bar-chart,
.doughnut-chart {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Specific styles for bar chart to ensure it fills the container */
.bar-chart-card .bar-chart :deep(canvas) {
    width: 100% !important;
    height: 100% !important;
}

.card-title {
    display: flex;
    align-items: center;
    justify-content: flex-start; /* Aligns title to the left */
    margin-bottom: 10px; /* Add space below title */
}

.card-title .icon {
    font-size: 20px; /* Adjust icon size as needed */
    margin-right: 5px; /* Space between icon and title */
}

.card-title {
    display: flex;
    align-items: center;
    justify-content: flex-start; /* Aligns title to the left */
    margin-bottom: 10px; /* Space below title */
}

.card-title h5 {
    font-weight: bold; /* Ensure it is bold */
    font-size: 1.25rem; /* Adjust the size to match other card titles */
    margin: 0; /* Remove default margin for alignment */
}

.card-title .icon {
    font-size: 20px; /* Adjust icon size as needed */
    margin-right: 5px; /* Space between icon and title */
}

.card-title {
    display: flex;
    align-items: center; /* Centers icon vertically with the label */
    justify-content: center; /* Centers title within the card */
    margin-bottom: 10px;
}

.card-title .icon {
    font-size: 20px;
    margin-right: 8px; /* Adjust space between icon and label for alignment */
}


</style>

