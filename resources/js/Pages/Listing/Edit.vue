<script setup>
import Container from "../../Components/Container.vue";
import Title from "../../Components/Title.vue";
import InputField from "../../Components/InputField.vue";
import TextArea from "../../Components/TextArea.vue";
import ImageUpload from "../../Components/ImageUpload.vue";
import ErrorMessages from "../../Components/ErrorMessages.vue";
import PrimaryBtn from "../../Components/PrimaryBtn.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({ listing: Object });

const form = useForm({
	name: props.listing.name,
	species: props.listing.species,
	desc: props.listing.desc,
	breed: props.listing.breed,
	color: props.listing.color,
	image: null,
	_method: "PUT",
});
</script>

<template>
	<Head title="- Edit Listing" />

	<Container>
		<div class="mb-6">
			<Title>Edit your listing</Title>
		</div>

		<ErrorMessages :errors="form.errors" />

		<form
			@submit.prevent="form.post(route('listing.update', listing.id))"
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
					label="Species"
					icon="dog"
					placeholder="Species"
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
				<div class="mt-1">
					<label class="block text-sm font-medium text-gray-400">Current Image</label>
					<img
						:src="
							listing.image
								? `/storage/${listing.image}`
								: '/storage/images/listing/default.jpg'
						"
						class="w-full h-52 object-cover object-center bg-slate-300 rounded-lg"
						alt="Current Image"
					/>
				</div>
				<ImageUpload @image="(e) => (form.image = e)" />
			</div>
			<div>
				<PrimaryBtn :disabled="form.processing">Update</PrimaryBtn>
			</div>
		</form>
	</Container>
</template>
