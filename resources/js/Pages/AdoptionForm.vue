<script setup>
import { useForm } from "@inertiajs/vue3";
import Container from "../Components/Container.vue";
import Title from "../Components/Title.vue";
import InputField from "../Components/InputField.vue";
import TextArea from "../Components/TextArea.vue";
import PrimaryBtn from "../Components/PrimaryBtn.vue";
import ErrorMessages from "../Components/ErrorMessages.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({
	listingId: Number,
});

const form = useForm({
	fullName: "",
	email: "",
	phone: "",
	address: "",
	dob: "",
	reason: "",
	listing_id: props.listingId, // Set the listing_id from props
});

const submitForm = () => {
	form.post(route("adoption.submit"));
};
</script>

<template>
	<Head title="Adoption Form" />

	<Container>
		<div class="mb-6">
			<Title>Adoption Form</Title>
		</div>

		<ErrorMessages :errors="form.errors" />

		<form @submit.prevent="submitForm" class="space-y-6">
			<InputField label="Full Name" v-model="form.fullName" />
			<InputField label="Email Address" v-model="form.email" />
			<InputField label="Phone Number" v-model="form.phone" />
			<InputField label="Address" v-model="form.address" />
			<InputField label="Date of Birth" type="date" v-model="form.dob" />
			<TextArea label="Reason for Adopting" v-model="form.reason" />

			<PrimaryBtn :disabled="form.processing">Submit</PrimaryBtn>
		</form>
	</Container>
</template>
