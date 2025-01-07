<script setup>
import PaginationLinks from "../../Components/PaginationLinks.vue";
import RoleSelect from "../../Components/RoleSelect.vue";
import SessionMessages from "../../Components/SessionMessages.vue";
import InputField from "../../Components/InputField.vue";
import { router, useForm } from "@inertiajs/vue3";

defineProps({ users: Object, adoptionApplications: Object, status: String });

const params = route().params;
const userForm = useForm({
	user_search: params.user_search,
	user_role: params.user_role,
});
const applicationForm = useForm({
	application_search: params.application_search,
	status: params.status || "all",
});

const searchUsers = () => {
	router.get(
		route("admin.index"),
		{
			user_search: userForm.user_search,
			user_role: userForm.user_role,
		},
		{ preserveScroll: true }
	);
};

const searchApplications = () => {
	router.get(
		route("admin.index"),
		{
			application_search: applicationForm.application_search,
			status: applicationForm.status,
		},
		{ preserveScroll: true }
	);
};

const toggleRole = (e) => {
	if (e.target.checked) {
		router.get(
			route("admin.index", {
				user_search: userForm.user_search,
				user_role: "suspended",
			}),
			{ preserveScroll: true }
		);
	} else {
		router.get(
			route("admin.index", {
				user_search: userForm.user_search,
				user_role: null,
			}),
			{ preserveScroll: true }
		);
	}
};

const deleteUser = (userId) => {
	if (window.confirm("Are you sure you want to delete this user?")) {
		router.delete(route("admin.destroy", userId), {
			preserveScroll: true,
			onSuccess: () => {},
			onError: () => {},
		});
	}
};
</script>

<template>
	<Head title="- Admin" />

	<SessionMessages :status="status" />

	<!-- Heading -->
	<div class="flex items-end justify-between mb-4">
		<div class="flex items-end gap-2">
			<!-- User Search form -->
			<form @submit.prevent="searchUsers">
				<InputField
					label=""
					icon="magnifying-glass"
					placeholder="Search users..."
					v-model="userForm.user_search"
				/>
			</form>
			<Link
				class="px-2 py-[6px] rounded-md bg-indigo-500 text-white flex items-center gap-2"
				v-if="params.user_search"
				:href="
					route('admin.index', {
						...params,
						user_search: null,
						page: null,
					})
				"
			>
				{{ params.user_search }}
				<i class="fa-solid fa-xmark"></i>
			</Link>
		</div>

		<!-- Toggle role btn -->
		<div
			class="flex items-center gap-1 text-xs hover:bg-slate-300 dark:hover:bg-slate-800 px-2 py-1 rounded-md"
		>
			<input
				@input="toggleRole"
				:checked="params.user_role"
				type="checkbox"
				id="toggleRole"
				class="rounded-md border-1 outline-0 text-indigo-500 ring-indigo-500 border-slate-700 cursor-pointer"
			/>
			<label
				for="toggleRole"
				class="block text-sm font-medium text-slate-700 dark:text-slate-300 cursor-pointer"
			>
				Show suspended users
			</label>
		</div>
	</div>

	<!-- Users Table -->
	<table
		class="bg-white dark:bg-slate-800 w-full rounded-lg overflow-hidden ring-1 ring-slate-300"
	>
		<thead>
			<tr class="bg-slate-600 text-slate-300 uppercase text-xs text-left">
				<th class="w-3/6 p-3 mr-auto">Name</th>
				<th class="w-2/6 p-3 mr-16">Role</th>
				<th class="w-1/6 p-3 ml-auto"></th>
			</tr>
		</thead>

		<tbody class="divide-y divide-slate-300 divide-dashed">
			<tr v-for="user in users.data" :key="user.id">
				<td class="w-3/6 py-5 px-3">
					<p class="font-bold mb-1">{{ user.name }}</p>
					<p class="font-light text-xs">{{ user.email }}</p>
				</td>

				<td class="w-2/6 py-5">
					<RoleSelect :user="user" />
				</td>

				<td class="w-1/6 py-5 px-3 text-right">
					<Link
						v-if="user.role === 'admin'"
						:href="route('user.show', user.id)"
						class="fa-solid fa-up-right-from-square px-3 text-indigo-400"
					></Link>
					<button
						@click="deleteUser(user.id)"
						class="fa-solid fa-trash text-red-500 px-3"
					></button>
				</td>
			</tr>
		</tbody>
	</table>

	<div class="mt-6">
		<PaginationLinks :paginator="users" />
	</div>

	<br />
	<br />

	<!-- Adoption Applications Table -->
	<div class="flex items-end justify-between mb-4">
		<div class="flex items-end gap-2">
			<!-- Application Search form -->

			<Link
				class="px-2 py-[6px] rounded-md bg-indigo-500 text-white flex items-center gap-2"
				v-if="params.application_search"
				:href="
					route('admin.index', {
						...params,
						application_search: null,
						page: null,
					})
				"
			>
				{{ params.application_search }}
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
				v-model="applicationForm.status"
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
						:href="route('admin.adoption.review', application.id)"
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
