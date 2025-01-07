<script setup>
import Title from "../Components/Title.vue";
import PaginationLinks from "../Components/PaginationLinks.vue";
import SessionMessages from "../Components/SessionMessages.vue";
import InputField from "../Components/InputField.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref, onMounted, computed } from "vue";
import { Bar } from "vue-chartjs";
import {
	Chart as ChartJS,
	Title as ChartTitle,
	Tooltip,
	Legend,
	BarElement,
	CategoryScale,
	LinearScale,
} from "chart.js";

ChartJS.register(ChartTitle, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps({
	listings: Object,
	allListings: Array, // Add allListings prop
	status: String,
	adoptionApplications: Array,
	search: String,
	species: String,
});

const params = route().params;
const form = useForm({ search: params.search, species: params.species });

// Computed metrics
const metrics = computed(() => {
	if (!props.allListings || !props.adoptionApplications) return null;

	const listingsData = props.allListings;
	const totalListings = listingsData.length;

	// Calculate metrics
	const availableListings = listingsData.filter((l) => l.status === "available").length;
	const pendingApplications = props.adoptionApplications.filter(
		(a) => a.status === "pending"
	).length;
	const adoptedListings = listingsData.filter((l) => l.status === "unavailable").length;

	// Calculate adoption rate (adopted / total)
	const adoptionRate = totalListings ? (adoptedListings / totalListings) * 100 : 0;

	// Calculate species distribution
	const speciesDistribution = listingsData.reduce((acc, listing) => {
		acc[listing.species] = (acc[listing.species] || 0) + 1;
		return acc;
	}, {});

	return {
		totalListings,
		availableListings,
		pendingApplications,
		adoptedListings,
		adoptionRate: adoptionRate.toFixed(1),
		speciesDistribution,
	};
});

// Chart data
const chartData = computed(() => {
	if (!metrics.value) return null;

	return {
		labels: ["Available", "Pending Applications", "Adopted"],
		datasets: [
			{
				label: "Listing Status Distribution",
				backgroundColor: ["#4CAF50", "#FFC107", "#2196F3"],
				data: [
					metrics.value.availableListings,
					metrics.value.pendingApplications,
					metrics.value.adoptedListings,
				],
			},
		],
	};
});

// Species chart data
const speciesChartData = computed(() => {
	if (!metrics.value?.speciesDistribution) return null;

	const distribution = metrics.value.speciesDistribution;
	return {
		labels: Object.keys(distribution),
		datasets: [
			{
				label: "Species Distribution",
				backgroundColor: [
					"#FF6384",
					"#36A2EB",
					"#FFCE56",
					"#4BC0C0",
					"#9966FF",
					"#FF9F40",
				],
				data: Object.values(distribution),
			},
		],
	};
});

const chartOptions = {
	responsive: true,
	maintainAspectRatio: false,
	plugins: {
		legend: {
			position: "bottom",
		},
	},
};

const searchListings = () => {
	router.get(route("dashboard"), {
		search: form.search,
		species: form.species,
	});
};

const deleteListing = (id) => {
	if (confirm("Are you sure?")) {
		router.delete(route("listing.destroy", id));
	}
};
</script>

<template>
	<Head title="- Dashboard" />

	<SessionMessages :status="status" />

	<div v-if="listings">
		<div v-if="Object.keys(listings.data).length">
			<div class="mb-6">
				<!-- Search Form -->
				<div class="flex items-center justify-between mb-4">
					<form @submit.prevent="searchListings" class="flex items-center">
						<InputField
							label=""
							icon="magnifying-glass"
							placeholder="Search listings..."
							v-model="form.search"
						/>
						<InputField
							label=""
							icon="paw"
							placeholder="Search species..."
							v-model="form.species"
							class="ml-2"
						/>
						<button
							type="submit"
							class="ml-2 px-4 py-1.5 mt-1 bg-indigo-500 text-white rounded-md hover:bg-indigo-600 transition-colors"
						>
							Search
						</button>
					</form>
				</div>

				<!-- Key Metrics -->
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
					<div class="p-6 bg-white rounded-lg shadow-md">
						<h3 class="text-lg font-semibold text-gray-600">Total Listings</h3>
						<p class="text-3xl font-bold text-indigo-600">{{ metrics.totalListings }}</p>
					</div>
					<div class="p-6 bg-white rounded-lg shadow-md">
						<h3 class="text-lg font-semibold text-gray-600">Available</h3>
						<p class="text-3xl font-bold text-green-600">
							{{ metrics.availableListings }}
						</p>
					</div>
					<div class="p-6 bg-white rounded-lg shadow-md">
						<h3 class="text-lg font-semibold text-gray-600">Pending Applications</h3>
						<p class="text-3xl font-bold text-yellow-600">
							{{ metrics?.pendingApplications || 0 }}
						</p>
					</div>
					<div class="p-6 bg-white rounded-lg shadow-md">
						<h3 class="text-lg font-semibold text-gray-600">Adoption Rate</h3>
						<p class="text-3xl font-bold text-blue-600">{{ metrics.adoptionRate }}%</p>
					</div>
				</div>

				<!-- Charts Section -->
				<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
					<div class="bg-white p-6 rounded-lg shadow-md">
						<h3 class="text-lg font-semibold mb-4">Status Distribution</h3>
						<div class="h-[300px]">
							<Bar v-if="chartData" :data="chartData" :options="chartOptions" />
						</div>
					</div>
					<div class="bg-white p-6 rounded-lg shadow-md">
						<h3 class="text-lg font-semibold mb-4">Species Distribution</h3>
						<div class="h-[300px]">
							<Bar
								v-if="speciesChartData"
								:data="speciesChartData"
								:options="chartOptions"
							/>
						</div>
					</div>
				</div>

				<!-- Listings Table -->
				<Title>Latest Rescues Listed</Title>
				<div class="bg-white rounded-lg shadow-md overflow-hidden">
					<table class="w-full table-fixed border-collapse text-sm">
						<thead class="bg-gray-50">
							<tr>
								<th class="w-3/4 p-4 text-left font-semibold text-gray-600">Name</th>
								<th class="w-3/4 p-4 text-left font-semibold text-gray-600">Species</th>
								<th class="w-1/2 p-4 text-left font-semibold text-gray-600">
									Date Created
								</th>
								<th class="w-1/4 p-4 text-right font-semibold text-gray-600">Actions</th>
							</tr>
						</thead>

						<tbody>
							<tr
								v-for="listing in listings.data"
								:key="listing.id"
								class="border-t border-gray-200 hover:bg-gray-50 transition-colors"
							>
								<td class="p-4">
									<div class="flex items-center gap-3">
										<img
											:src="
												listing.image
													? `/storage/${listing.image}`
													: '/storage/images/listing/default.jpg'
											"
											alt=""
											class="h-10 w-10 rounded-full object-cover"
										/>
										<span class="font-medium text-gray-900">{{ listing.name }}</span>
									</div>
								</td>
								<td class="p-4 text-gray-600">{{ listing.species }}</td>
								<td class="p-4 text-gray-600">
									{{ new Date(listing.created_at).toLocaleDateString() }}
								</td>
								<td class="p-4 text-right space-x-2">
									<Link
										:href="route('listing.show', listing.id)"
										class="inline-flex items-center px-3 py-1 text-sm font-medium text-indigo-600 hover:text-indigo-800"
									>
										View
									</Link>
									<Link
										:href="route('listing.edit', listing.id)"
										class="inline-flex items-center px-3 py-1 text-sm font-medium text-green-600 hover:text-green-800"
									>
										Edit
									</Link>
									<button
										@click="deleteListing(listing.id)"
										class="inline-flex items-center px-3 py-1 text-sm font-medium text-red-600 hover:text-red-800"
									>
										Delete
									</button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="mt-6">
					<PaginationLinks :paginator="listings" />
				</div>
			</div>
		</div>
		<div v-else class="text-center py-12 text-gray-600">
			No listings found. Create your first listing to get started!
		</div>
	</div>
	<div v-else class="text-center py-12">
		Due to violation of our terms your account has been suspended, please contact us at
		<span class="text-indigo-600">email@admin.com</span>
	</div>
</template>
