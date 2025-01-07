<script setup>
import { Head } from "@inertiajs/vue3";
import AdminLayout from "../../Layouts/AdminLayout.vue";
</script>

<template>
	<Head title="Admin Dashboard" />

	<AdminLayout>
		<div>
			<h1 class="text-2xl font-bold">Welcome to the Admin Dashboard</h1>
			<p class="mt-4">Use the sidebar to navigate to different sections.</p>
		</div>
	</AdminLayout>
</template>
