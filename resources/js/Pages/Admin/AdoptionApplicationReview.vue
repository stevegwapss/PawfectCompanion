<script setup>
import { defineProps, computed } from "vue";
import { router } from "@inertiajs/vue3";
import Container from "../../Components/Container.vue";
import Title from "../../Components/Title.vue";
import ListingCard from "../../Components/ListingCard.vue";

const props = defineProps({
	application: Object,
});

const approveApplication = () => {
	if (window.confirm("Are you sure you want to approve this application?")) {
		router.put(route("admin.adoption.approve", props.application.id));
	}
};

const disapproveApplication = () => {
	if (window.confirm("Are you sure you want to disapprove this application?")) {
		router.put(route("admin.adoption.disapprove", props.application.id));
	}
};

const statusText = computed(() => {
	if (props.application.status === "approved") {
		return "This application has been approved.";
	} else if (props.application.status === "disapproved") {
		return "This application has been disapproved.";
	} else {
		return "This application is pending approval.";
	}
});
</script>

<template>
	<Head title="Adoption Application Review" />

	<div class="mb-1 rounded-lg p-4 flex justify-between items-center gap-4 bg-slate-800">
		<p class="text-white">{{ statusText }}</p>
		<div class="flex gap-4">
			<button
				v-if="props.application.status !== 'approved'"
				@click="approveApplication"
				class="bg-green-500 text-white px-4 py-2 w-32 rounded-md"
			>
				Approve
			</button>
			<button
				v-if="props.application.status !== 'disapproved'"
				@click="disapproveApplication"
				class="bg-red-500 text-white px-4 py-2 rounded-md"
			>
				Disapprove
			</button>
		</div>
	</div>

	<Container class="flex gap-4">
		<div class="w-3/4">
			<div class="mb-6">
				<Title>Adoption Application Review</Title>
			</div>

			<!-- Form Container -->
			<div class="space-y-4">
				<div><strong>Pet:</strong> {{ application.listing.name }}</div>
				<br />
				<div><strong>Full Name:</strong> {{ application.fullName }}</div>
				<div><strong>Email:</strong> {{ application.email }}</div>
				<div><strong>Phone:</strong> {{ application.phone }}</div>
				<div><strong>Address:</strong> {{ application.address }}</div>
				<div><strong>Date of Birth:</strong> {{ application.dob }}</div>
				<div><strong>Reason for Adopting:</strong> {{ application.reason }}</div>
			</div>
		</div>

		<div class="w-1/4">
			<ListingCard :listing="application.listing" />
		</div>
	</Container>
</template>
