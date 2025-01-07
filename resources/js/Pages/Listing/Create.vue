<script setup>
import Container from "../../Components/Container.vue";
import Title from "../../Components/Title.vue";
import InputField from "../../Components/InputField.vue";
import TextArea from "../../Components/TextArea.vue";
import ImageUpload from "../../Components/ImageUpload.vue";
import ErrorMessages from "../../Components/ErrorMessages.vue";
import PrimaryBtn from "../../Components/PrimaryBtn.vue";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
	name: null,
	desc: null,
	species: null,
	breed: null,
	color: null,
	image: null,
});
</script>

<template>
	<Head title="- New Listing" />

	<Container>
		<div class="mb-6">
			<Title>Create a new listing</Title>
		</div>

		<ErrorMessages :errors="form.errors" />

		<form
			@submit.prevent="form.post(route('listing.store'))"
			class="grid grid-cols-2 gap-6"
		>
			<div class="space-y-6">
				<InputField
					label="Name"
					icon="heading"
					placeholder="Listing name"
					v-model="form.name"
				/>
				<InputField
					label="species"
					icon="dog"
					placeholder="species"
					v-model="form.species"
				/>

				<InputField label="Breed" icon="paw" placeholder="Breed" v-model="form.breed" />

				<InputField
					label="Color"
					icon="palette"
					placeholder="Color"
					v-model="form.color"
				/>

				<TextArea
					label="Description"
					icon="newspaper"
					placeholder="This is my listing description"
					v-model="form.desc"
				/>
			</div>

			<div class="space-y-6">
				<ImageUpload @image="(e) => (form.image = e)" />
			</div>
			<div>
				<PrimaryBtn :disabled="form.processing">Create</PrimaryBtn>
			</div>
		</form>
	</Container>
</template>
