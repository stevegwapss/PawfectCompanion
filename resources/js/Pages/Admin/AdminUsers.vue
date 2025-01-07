<script setup>
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import PaginationLinks from "../../Components/PaginationLinks.vue";
import RoleSelect from "../../Components/RoleSelect.vue";
import SessionMessages from "../../Components/SessionMessages.vue";
import InputField from "../../Components/InputField.vue";

defineProps({ users: Object, status: String });

const form = useForm({
	search: route().params.search || "",
	user_role: route().params.user_role || "",
});

const searchUsers = () => {
	router.get(
		route("admin.users"),
		{
			search: form.search,
			user_role: form.user_role,
		},
		{
			preserveState: true,
			preserveScroll: true,
			replace: true,
		}
	);
};

const toggleRole = (e) => {
	form.user_role = e.target.checked ? "suspended" : "";
	searchUsers();
};

const deleteUser = (userId) => {
	if (window.confirm("Are you sure you want to delete this user?")) {
		router.delete(route("admin.destroy", { user: userId }), {
			preserveScroll: true,
		});
	}
};
</script>

<template>
	<Head title="Admin - Users" />

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
					v-model="form.search"
					@input="searchUsers"
				/>
			</form>
			<Link
				v-if="form.search"
				class="px-2 py-[6px] rounded-md bg-indigo-500 text-white flex items-center gap-2"
				:href="route('admin.users')"
				@click="
					form.search = '';
					searchUsers();
				"
			>
				{{ form.search }}
				<i class="fa-solid fa-xmark"></i>
			</Link>
		</div>

		<!-- Toggle role btn -->
		<div
			class="flex items-center gap-1 text-xs hover:bg-slate-300 dark:hover:bg-slate-800 px-2 py-1 rounded-md"
		>
			<input
				@change="toggleRole"
				:checked="form.user_role === 'suspended'"
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

	<!-- Rest of the template remains the same -->
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
						:href="route('user.show', { user: user.id })"
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
</template>
