<script setup>
import { Head } from "@inertiajs/vue3";
import Container from "../Components/Container.vue";
import Title from "../Components/Title.vue";
import ListingCard from "../Components/ListingCard.vue";
import AppointmentScheduler from "../Components/AppointmentScheduler.vue";
import { computed, ref } from "vue";
import Footer from "../Components/Footer.vue";

const props = defineProps({
  application: {
    type: Object,
    required: true
  }
});

const application = ref(props.application);
const isRescheduling = ref(false);

const statusText = computed(() => {
  if (!application.value) return '';
  
  switch (application.value.status) {
    case 'approved':
      return 'Your application has been approved!';
    case 'disapproved':
      return 'Your application has been disapproved.';
    default:
      return 'Your application is under review.';
  }
});

const appointmentStatusMessage = computed(() => {
  const appointment = application.value?.appointment;
  if (!appointment) return "No appointment scheduled.";

  switch (appointment.appointment_status) {
    case 'pending':
      return "Pending appointment approval.";
    case 'approved':
      return "Your appointment has been scheduled.";
    case 'disapproved':
      return "Your appointment has been disapproved.";
    case 'cancelled':
      return "Your appointment has been cancelled.";
    default:
      return "No appointment scheduled.";
  }
});

const canReschedule = computed(() => {
  const status = application.value?.appointment?.appointment_status;
  return status === 'disapproved' || status === 'cancelled';
});

const canCancel = computed(() => {
  const status = application.value?.appointment?.appointment_status;
  return status === 'pending';
});

const hasAppointment = computed(() => {
  return !!application.value?.appointment;
});

const appointmentStatusClass = computed(() => {
  const status = application.value?.appointment?.appointment_status;
  return {
    'text-yellow-500': status === 'pending',
    'text-green-500': status === 'approved',
    'text-red-500': status === 'disapproved' || status === 'cancelled'
  };
});

const formatDate = (date) => {
  return new Date(date).toLocaleString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: 'numeric',
    minute: 'numeric',
    hour12: true
  });
};

const handleAppointmentScheduled = (newAppointment) => {
  application.value = {
    ...application.value,
    appointment: newAppointment
  };
  isRescheduling.value = false;
};

const handleCancelClick = () => {
  isRescheduling.value = true;
};
</script>

<template>
  <Head title="Adoption Application Overview" />

  <!-- Status Message -->
  <div class="mb-2 shadow-lg rounded-lg p-4 bg-slate-800 text-white">
    <template v-if="application">
      <p v-if="application.status === 'approved'">
        <span class="text-green-400">{{ statusText }}</span>
        <br /><br />
        Thank you for adopting! We are thrilled to have found a new home for
        <span class="text-sky-400 font-medium">{{ application.listing.name }}</span>.
        <br />
        Please schedule an appointment to meet your beloved pet.
        <br>
        <br>
        <p>{{ appointmentStatusMessage }}</p>
          
        <br /><br />
        <div class="space-y-1">
          <div>Shelter Location: Don M.C Enriquez dr. Tetuan Zamboanga City, 7000</div>
          <div>Time Open: 8:00 AM - 5:00 PM</div>
          <div>Email: PawfectCompanion@gmail.com</div>
          <div>Contact #: 0123456789</div>
        </div>
      </p>
      <p v-else-if="application.status === 'disapproved'">
        <span class="text-red-600">{{ statusText }}</span>
        <br /><br />
        We regret to inform you that your adoption application for
        <span class="text-sky-400 font-medium">{{ application.listing.name }}</span>
        has been disapproved.
      </p>
    </template>
  </div>

  <Container class="flex gap-4">
    <div class="w-3/4">
      <div class="mb-6">
        <Title>Adoption Application Overview</Title>
      </div>

      <!-- Application Details -->
      <div v-if="application" class="space-y-4">
        <div class="grid gap-4">
          <div><strong>Pet:</strong> {{ application.listing.name }}</div>
          <div><strong>Full Name:</strong> {{ application.fullName }}</div>
          <div><strong>Email:</strong> {{ application.email }}</div>
          <div><strong>Phone:</strong> {{ application.phone }}</div>
          <div><strong>Address:</strong> {{ application.address }}</div>
          <div><strong>Date of Birth:</strong> {{ application.dob }}</div>
          <div><strong>Reason for Adopting:</strong> {{ application.reason }}</div>
        </div>

        <!-- Appointment Section -->
        <div v-if="application.status === 'approved'" class="mt-6">
          <div v-if="hasAppointment && !isRescheduling">
            <h2 class="text-xl font-bold mb-3">Appointment Details</h2>
            <div class="space-y-2">
              <p class="font-medium" :class="appointmentStatusClass">
                {{ appointmentStatusMessage }}
              </p>
              <p v-if="application.appointment?.appointment_date">
                Appointment Date: {{ formatDate(application.appointment.appointment_date) }}
              </p>
              <div class="space-x-2">
                <button
                  v-if="canReschedule"
                  @click="isRescheduling = true"
                  class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded transition"
                >
                  Reschedule Appointment
                </button>
                <button
                  v-if="canCancel"
                  @click="handleCancelClick"
                  class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition"
                >
                  Cancel Appointment
                </button>
              </div>
            </div>
          </div>
          <div v-else>
            <h2 class="text-xl font-bold mb-3">{{ isRescheduling ? 'Reschedule' : 'Schedule' }} an Appointment</h2>
            <AppointmentScheduler 
              :application-id="application.id"
              @appointment-scheduled="handleAppointmentScheduled"
              :is-rescheduling="isRescheduling"
            />
          </div>
        </div>
      </div>
    </div>

    <div class="w-1/4">
      <ListingCard v-if="application" :listing="application.listing" />
    </div>
  </Container>

  <Footer />
</template>