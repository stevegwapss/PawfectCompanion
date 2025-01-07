<script setup>
import { ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
	appointments: {
		type: Array,
		required: true,
	},
});

const appointments = ref(props.appointments);
const isLoading = ref(false);
const errorMessage = ref(null);

// Watch for changes in props and update the local ref
watch(
	() => props.appointments,
	(newAppointments) => {
		appointments.value = newAppointments;
	},
	{ deep: true }
);

const formatDate = (date) => {
	return new Date(date).toLocaleString();
};

const handleAppointment = async (id, action) => {
	try {
		isLoading.value = true;
		await router.post(
			`/admin/appointments/${id}/${action}`,
			{},
			{
				preserveScroll: true,
				preserveState: false,
				onSuccess: () => {
					router.visit(route("admin.appointments"), {
						preserveScroll: true,
						preserveState: false,
						replace: true,
					});
					errorMessage.value = null;
				},
				onError: (errors) => {
					errorMessage.value = errors.message || "An error occurred";
				},
			}
		);
	} catch (error) {
		console.error("Error handling appointment:", error);
		errorMessage.value = "An error occurred while processing your request";
	} finally {
		isLoading.value = false;
	}
};

const cancelAppointment = async (appointmentId) => {
	try {
		isLoading.value = true;
		const response = await axios.post(`/admin/appointments/${appointmentId}/cancel`);

		if (response.data.success) {
			errorMessage.value = null;
			router.visit(route("admin.appointments"), {
				preserveScroll: true,
				preserveState: false,
				replace: true,
			});
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
</script>

<template>
	<div class="p-6">
		<h1 class="text-2xl font-bold mb-6">Manage Appointments</h1>

		<div
			v-if="errorMessage"
			class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"
		>
			{{ errorMessage }}
		</div>

		<div class="bg-white rounded-lg shadow overflow-hidden text-black">
			<table class="min-w-full divide-y divide-gray-200">
				<thead class="bg-slate-600 uppercase text-xs">
					<tr>
						<th
							class="px-4 py-2 text-left text-xs font-medium text-slate-300 uppercase tracking-wider"
						>
							Pet
						</th>
						<th
							class="px-4 py-2 text-left text-xs font-medium text-slate-300 uppercase tracking-wider"
						>
							Full Name
						</th>
						<th
							class="px-4 py-2 text-left text-xs font-medium text-slate-300 uppercase tracking-wider"
						>
							Email
						</th>
						<th
							class="px-4 py-2 text-left text-xs font-medium text-slate-300 uppercase tracking-wider"
						>
							Appointment Date
						</th>
						<th
							class="px-6 py-2 text-left text-xs font-medium text-slate-300 uppercase tracking-wider"
						>
							Status
						</th>
						<th
							class="px-4 py-2 text-left text-xs font-medium text-slate-300 uppercase tracking-wider"
						>
							Actions
						</th>
					</tr>
				</thead>
				<tbody class="bg-white divide-y divide-gray-200">
					<tr v-for="appointment in appointments" :key="appointment.id">
						<td class="px-4 text-xs py-2 whitespace-nowrap">
							{{ appointment.listing_name }}
						</td>
						<td class="px-4 text-xs py-2 whitespace-nowrap">
							{{ appointment.fullName }}
						</td>
						<td class="px-4 text-xs py-2 whitespace-nowrap">{{ appointment.email }}</td>
						<td class="px-4 text-xs py-5 whitespace-nowrap">
							{{ formatDate(appointment.appointment_date) }}
						</td>
						<td class="px-4 text-sm py-2 whitespace-nowrap">
							<span
								:class="{
									'px-2 py-1 text-xs rounded-full': true,
									'bg-yellow-100 text-yellow-800':
										appointment.appointment_status === 'pending',
									'bg-green-100 text-green-800':
										appointment.appointment_status === 'approved',
									'bg-red-100 text-red-800':
										appointment.appointment_status === 'disapproved',
									'bg-blue-100 text-blue-800':
										appointment.appointment_status === 'cancelled',
								}"
							>
								{{ appointment.appointment_status }}
							</span>
						</td>
						<td class="px-4 py-2 whitespace-nowrap space-x-2">
							<button
								v-if="appointment.appointment_status === 'pending'"
								@click="handleAppointment(appointment.id, 'approve')"
								:disabled="isLoading"
								class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs transition disabled:opacity-50"
							>
								Approve
							</button>
							<button
								v-if="appointment.appointment_status === 'pending'"
								@click="handleAppointment(appointment.id, 'disapprove')"
								:disabled="isLoading"
								class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs transition disabled:opacity-50"
							>
								Disapprove
							</button>
							<button
								v-if="appointment.appointment_status === 'approved'"
								@click="cancelAppointment(appointment.id)"
								:disabled="isLoading"
								class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs transition disabled:opacity-50"
							>
								Cancel
							</button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</template>

<style scoped>
.disabled {
	opacity: 0.5;
	cursor: not-allowed;
}
</style>
