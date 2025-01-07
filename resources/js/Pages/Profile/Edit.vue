<script setup>
import UpdateInfo from "./Sections/UpdateInfo.vue";
import UpdatePassword from "./Sections/UpdatePassword.vue";
import DeleteAccount from "./Sections/DeleteAccount.vue";
import Footer from "../../Components/Footer.vue";

defineProps({
	user: Object,
	status: String,
});
</script>

<template>
	<Head title="- Profile" />
	<UpdateInfo :user="user" :status="status" />
	<UpdatePassword />
	<DeleteAccount />
	<Footer />
</template>
