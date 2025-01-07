<script setup>
import { ref, watch, computed } from "vue";
import axios from "axios";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

const props = defineProps({
	applicationId: {
		type: Number,
		required: true,
	},
	isRescheduling: {
		type: Boolean,
		default: false,
	},
});

const emit = defineEmits(["appointment-scheduled"]);

const selectedDate = ref(null);
const selectedTime = ref(null);
const errorMessage = ref(null);
const isLoading = ref(false);
const existingDates = ref([]);

const timeSlots = computed(() => {
	const slots = [];
	for (let hour = 9; hour < 17; hour++) {
		const period = hour >= 12 ? "PM" : "AM";
		const displayHour = hour > 12 ? hour - 12 : hour;
		const militaryHour = hour.toString().padStart(2, "0");
		slots.push({
			display: `${displayHour}:00 ${period}`,
			value: `${militaryHour}:00`,
		});
	}
	return slots;
});

const fetchExistingDates = async () => {
	try {
		const response = await axios.get("/appointments/available-slots", {
			params: { date: selectedDate.value?.toISOString() },
		});

		if (response.data?.success) {
			existingDates.value = response.data.existingDates.map((date) => new Date(date));
		}
	} catch (error) {
		console.error("Error fetching existing dates:", error);
	}
};

const disabledDates = (date) => {
	const today = new Date();
	today.setHours(0, 0, 0, 0);

	const isExisting = existingDates.value.some(
		(existingDate) => existingDate.toDateString() === date.toDateString()
	);

	return date < today || isWeekend(date) || isExisting;
};

const isWeekend = (date) => {
	const day = date.getDay();
	return day === 0 || day === 6;
};

const scheduleAppointment = async () => {
	if (!selectedDate.value || !selectedTime.value) {
		errorMessage.value = "Please select both date and time for the appointment.";
		return;
	}

	try {
		isLoading.value = true;

		const appointmentDate = new Date(selectedDate.value);
		const [hours, minutes] = selectedTime.value.split(":");
		appointmentDate.setHours(parseInt(hours), parseInt(minutes), 0, 0);

		const response = await axios.post(`/applications/${props.applicationId}/schedule`, {
			appointment_date: appointmentDate.toISOString(),
			is_rescheduling: props.isRescheduling,
		});

		if (response.data.success) {
			errorMessage.value = null;
			emit("appointment-scheduled", response.data.appointment);
		} else {
			errorMessage.value = response.data.message;
		}
	} catch (error) {
		console.error("Appointment scheduling error:", error);
		errorMessage.value =
			error.response?.data?.message ||
			"An error occurred while scheduling the appointment.";
	} finally {
		isLoading.value = false;
	}
};

const cancelAppointment = async () => {
	try {
		isLoading.value = true;
		const response = await axios.post(
			`/applications/${props.applicationId}/cancel-appointment`
		);

		if (response.data.success) {
			errorMessage.value = null;
			emit("appointment-scheduled", response.data.appointment);
		} else {
			errorMessage.value = response.data.message;
		}
	} catch (error) {
		console.error("Appointment cancellation error:", error);
		errorMessage.value = error.response?.data?.message || "Failed to cancel appointment";
	} finally {
		isLoading.value = false;
	}
};

watch(
	() => selectedDate.value,
	(newDate) => {
		selectedTime.value = null;
		if (newDate) {
			fetchExistingDates();
		}
	}
);

const buttonText = computed(() => {
	return props.isRescheduling ? "Reschedule Appointment" : "Schedule Appointment";
});
</script>

<template>
	<div class="appointment-scheduler">
		<!-- Date Picker -->
		<div class="mb-4">
			<label class="block text-sm font-medium text-gray-700 mb-1">Select Date</label>
			<Datepicker
				v-model="selectedDate"
				:disabled-dates="disabledDates"
				:enable-time-picker="false"
				auto-apply
				class="border rounded p-2 min-w-80"
				timezone="Asia/Manila"
			/>
		</div>

		<!-- Time Slots -->
		<div v-if="selectedDate" class="mb-4">
			<label class="block text-sm font-medium text-gray-700 mb-1">Select Time</label>
			<div class="grid grid-cols-4 gap-2">
				<button
					v-for="slot in timeSlots"
					:key="slot.value"
					@click="selectedTime = slot.value"
					:class="[
						'px-3 py-2 text-sm rounded-md border',
						selectedTime === slot.value
							? 'bg-blue-500 text-white border-blue-500'
							: 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
					]"
				>
					{{ slot.display }}
				</button>
			</div>
		</div>

		<div class="flex gap-2">
			<!-- Schedule/Reschedule Button -->
			<button
				@click="scheduleAppointment"
				:disabled="isLoading || !selectedDate || !selectedTime"
				class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition disabled:opacity-50 disabled:cursor-not-allowed"
			>
				{{ buttonText }}
			</button>

			<!-- Cancel Button (Only show for pending appointments) -->
			<button
				v-if="props.isRescheduling"
				@click="cancelAppointment"
				:disabled="isLoading"
				class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition disabled:opacity-50 disabled:cursor-not-allowed"
			>
				Cancel Appointment
			</button>
		</div>

		<!-- Error Message -->
		<p v-if="errorMessage" class="text-red-600 mt-2">{{ errorMessage }}</p>
	</div>
</template>

<style scoped>
.appointment-scheduler {
	@apply space-y-4;
}

:deep(.dp__main) {
	@apply w-full;
}

:deep(.dp__input) {
	@apply w-full py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500;
}
</style>
