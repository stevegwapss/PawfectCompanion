<script setup>
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import PaginationLinks from "../../Components/PaginationLinks.vue";
import SessionMessages from "../../Components/SessionMessages.vue";
import InputField from "../../Components/InputField.vue";

defineProps({ adoptionApplications: Object, status: String });

const form = useForm({
	search: route().params.search || "",
	status: route().params.status || "all",
});

const searchApplications = () => {
	router.get(
		route("admin.adoption.applications"),
		{
			search: form.search,
			status: form.status,
		},
		{
			preserveState: true,
			preserveScroll: true,
			replace: true,
		}
	);
};
</script>

<template>
	<Head title="Admin - Adoption Applications" />

	<SessionMessages :status="status" />

	<!-- Heading -->
	<div class="flex items-end justify-between mb-4">
		<div class="flex items-end gap-2">
			<!-- Application Search form -->
			<form @submit.prevent="searchApplications">
				<InputField
					label=""
					icon="magnifying-glass"
					placeholder="Search applications..."
					v-model="form.search"
					@input="searchApplications"
				/>
			</form>
			<Link
				v-if="form.search"
				class="px-2 py-[6px] rounded-md bg-indigo-500 text-white flex items-center gap-2"
				:href="route('admin.adoption.applications', { status: form.status })"
				@click="
					form.search = '';
					searchApplications();
				"
			>
				{{ form.search }}
				<i class="fa-solid fa-xmark"></i>
			</Link>
		</div>

		<!-- Status filter -->
		<div class="flex items-center gap-2">
			<label
				for="statusFilter"
				class="block text-sm font-medium text-slate-700 dark:text-slate-300"
				>Status:</label
			>
			<select
				id="statusFilter"
				v-model="form.status"
				@change="searchApplications"
				class="rounded-md border-1 outline-0 text-indigo-500 ring-indigo-500 border-slate-700 cursor-pointer"
			>
				<option value="all">All</option>
				<option value="approved">Approved</option>
				<option value="pending">Pending</option>
				<option value="disapproved">Disapproved</option>
			</select>
		</div>
	</div>

	<!-- Rest of the template remains the same -->
	<!-- Adoption Applications Table -->
	<table
		class="bg-white dark:bg-slate-800 w-full rounded-lg overflow-hidden ring-1 ring-slate-300 mt-6"
	>
		<thead>
			<tr class="bg-slate-600 text-slate-300 uppercase text-xs text-left">
				<th class="w-1/6 p-3 mr-auto">Full Name</th>
				<th class="w-2/6 p-3 mr-16">Email</th>
				<th class="w-1/6 p-3 ml-auto">Phone</th>
				<th class="w-1/6 p-3 ml-auto">Pet</th>
				<th class="w-1/6 p-3 ml-auto">Status</th>
				<th class="w-1/6 p-3 ml-auto"></th>
			</tr>
		</thead>
		<tbody>
			<tr v-for="application in adoptionApplications.data" :key="application.id">
				<td class="p-4 font-bold">{{ application.fullName }}</td>
				<td class="p-3">{{ application.email }}</td>
				<td class="p-3">{{ application.phone }}</td>
				<td class="p-3">{{ application.listing.name }}</td>
				<td class="pl-7 text-left">
					<i
						v-if="application.status === 'approved'"
						class="fa-solid fa-check text-green-500"
					></i>
					<i
						v-if="application.status === 'disapproved'"
						class="fa-solid fa-times text-red-500"
					></i>
					<i
						v-if="application.status === 'pending'"
						class="fa-solid fa-hourglass-half text-yellow-500"
					></i>
				</td>
				<td class="p-3 text-right">
					<Link
						:href="route('admin.adoption.review', { application: application.id })"
						class="fa-solid fa-up-right-from-square px-3 text-indigo-400"
					></Link>
				</td>
			</tr>
		</tbody>
	</table>

	<div class="mt-6">
		<PaginationLinks :paginator="adoptionApplications" />
	</div>
</template>
