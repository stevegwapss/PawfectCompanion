<script setup>
import PaginationLinks from "../../Components/PaginationLinks.vue";
import SessionMessages from "../../Components/SessionMessages.vue";
import InputField from "../../Components/InputField.vue";
import { router, useForm } from "@inertiajs/vue3";
import AdminLayout from "../../Layouts/AdminLayout.vue";

defineProps({ listings: Object, status: String });

const params = route().params;
const listingForm = useForm({ listing_search: params.listing_search });

const searchListings = () => {
	router.get(
		route("admin.listings"),
		{
			listing_search: listingForm.listing_search,
		},
		{ preserveScroll: true }
	);
};
</script>

<template>
	<Head title="Admin - Listings" />

	<AdminLayout>
		<SessionMessages :status="status" />

		<!-- Heading -->
		<div class="flex items-end justify-between mb-4">
			<div class="flex items-end gap-2">
				<!-- Listing Search form -->
				<form @submit.prevent="searchListings">
					<InputField
						label=""
						icon="magnifying-glass"
						placeholder="Search listings..."
						v-model="listingForm.listing_search"
					/>
				</form>
				<Link
					class="px-2 py-[6px] rounded-md bg-indigo-500 text-white flex items-center gap-2"
					v-if="params.listing_search"
					:href="
						route('admin.listings', {
							...params,
							listing_search: null,
							page: null,
						})
					"
				>
					{{ params.listing_search }}
					<i class="fa-solid fa-xmark"></i>
				</Link>
			</div>
		</div>

		<!-- Listings Table -->
		<table
			class="bg-white dark:bg-slate-800 w-full rounded-lg overflow-hidden ring-1 ring-slate-300"
		>
			<thead>
				<tr class="bg-slate-600 text-slate-300 uppercase text-xs text-left">
					<th class="w-3/6 p-3 mr-auto">Name</th>
					<th class="w-2/6 p-3 mr-16">Owner</th>
					<th class="w-1/6 p-3 ml-auto"></th>
				</tr>
			</thead>

			<tbody class="divide-y divide-slate-300 divide-dashed">
				<tr v-for="listing in listings.data" :key="listing.id">
					<td class="w-3/6 py-5 px-3">
						<p class="font-bold mb-1">{{ listing.name }}</p>
						<p class="font-light text-xs">{{ listing.desc }}</p>
					</td>

					<td class="w-2/6 py-5">
						<p class="font-light text-xs">{{ listing.user.name }}</p>
					</td>

					<td class="w-1/6 py-5 px-3 text-right">
						<Link
							:href="route('listing.show', listing.id)"
							class="fa-solid fa-up-right-from-square px-3 text-indigo-400"
						></Link>
					</td>
				</tr>
			</tbody>
		</table>

		<div class="mt-6">
			<PaginationLinks :paginator="listings" />
		</div>
	</AdminLayout>
</template>
