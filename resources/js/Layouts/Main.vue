<script setup>
import { switchTheme } from "../theme";
import NavLink from "../Components/NavLink.vue";
import { usePage } from "@inertiajs/vue3";
import { computed, ref, onMounted, onBeforeUnmount } from "vue";
import axios from "axios";

const page = usePage();
const user = computed(() => page.props.auth.user);
const notifications = computed(() => page.props.notifications || []);

const show = ref(false);
const showNotifications = ref(false);

const markAsRead = (notificationId) => {
	axios.post(route("notifications.read", notificationId)).then(() => {
		const index = notifications.value.findIndex((n) => n.id === notificationId);
		if (index !== -1) {
			notifications.value.splice(index, 1);
		}
	});
};

const toggleNotifications = () => {
	showNotifications.value = !showNotifications.value;
};

const handleClickOutside = (event) => {
	if (showNotifications.value && !event.target.closest(".notification-container")) {
		showNotifications.value = false;
	}
};

onMounted(() => {
	document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
	document.removeEventListener("click", handleClickOutside);
});

// Safe getter for nested properties
const getNotificationData = (notification) => {
	try {
		return {
			message: notification?.data?.message || "No message available",
			color: notification?.data?.color || "black",
			date: notification?.data?.date || new Date().toISOString(),
			application_id: notification?.data?.application_id,
			appointment_id: notification?.data?.appointment_id,
		};
	} catch (error) {
		console.error("Error accessing notification data:", error);
		return {
			message: "Error loading notification",
			color: "black",
			date: new Date().toISOString(),
			application_id: null,
			appointment_id: null,
		};
	}
};
</script>

<template>
	<!-- overlay -->
	<div
		v-show="show"
		@click="show = false"
		class="fixed inset-0 z-40 bg-black bg-opacity-50 transition-opacity"
	></div>

	<header class="bg-slate-900 text-white h-16 sticky top-0 z-50 w-full">
		<nav class="p-1 w-full flex items-center ml-2 justify-between">
			<div class="flex items-center space-x-4">
				<NavLink routeName="home">
					<img src="/storage/app/public/images/listing/logopaw.svg" class="w-36" />
				</NavLink>
			</div>

			<div class="flex items-center space-x-4 mr-8">
				<!-- Auth -->
				<!-- Notifications -->
				<div v-if="user" class="relative notification-container">
					<button
						@click="toggleNotifications"
						class="hover:bg-slate-700 w-6 h-6 grid place-items-center rounded-full hover:outline outline-1 mt-1 outline-white"
					>
						<i class="fa-solid fa-bell"></i>
						<span
							v-if="notifications.length"
							class="absolute top-0 right-0 mt-1 inline-block w-2 h-2 bg-red-600 rounded-full"
						></span>
					</button>
					<div
						v-show="showNotifications"
						class="absolute mt-2 right-0 w-96 bg-white rounded-md shadow-lg z-50"
					>
						<div class="p-2 border-b text-black font-bold">Notifications</div>
						<div class="notifications-list max-h-[400px] overflow-y-auto">
							<ul>
								<li v-if="!notifications.length" class="p-2 text-center text-gray-500">
									No new notifications
								</li>
								<li
									v-for="notification in notifications"
									:key="notification.id"
									class="p-2 border-b hover:bg-gray-50"
								>
									<div class="flex justify-between items-center">
										<div class="flex-1 min-w-0">
											<p
												:style="{ color: getNotificationData(notification).color }"
												class="text-black break-words"
											>
												{{ getNotificationData(notification).message }}
											</p>
											<p class="text-gray-500 text-sm">
												{{
													new Date(
														getNotificationData(notification).date
													).toLocaleString()
												}}
											</p>
											<br />
											<NavLink
												v-if="
													getNotificationData(notification).appointment_id ||
													getNotificationData(notification).application_id
												"
												:href="
													route('adoption.overview', {
														application:
															getNotificationData(notification).application_id ||
															getNotificationData(notification).appointment_id,
													})
												"
												class="text-blue-500 hover:bg-slate-200 px-2 py-1 rounded"
												@click="showNotifications = false"
											>
												<i class="fa-solid fa-calendar-check mr-2"></i>
												View Application
											</NavLink>
										</div>
										<button
											@click="markAsRead(notification.id)"
											class="text-green-500 ml-2 p-1 hover:bg-green-100 rounded-lg w-10 h-10 flex items-center justify-center flex-shrink-0"
										>
											<i class="fa-solid fa-check-double"></i>
										</button>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>

				<button
					@click="switchTheme"
					class="hover:bg-slate-700 w-6 h-6 grid place-items-center rounded-full hover:outline outline-1 outline-white mt-1"
				>
					<i class="fa-solid fa-circle-half-stroke"></i>
				</button>
				<div v-if="user" class="relative flex items-center gap-4">
					<div
						@click="show = !show"
						class="flex items-center gap-2 px-3 py-1 rounded-lg hover:bg-slate-700 cursor-pointer"
						:class="{ 'bg-slate-700': show }"
					>
						<p>{{ user.name }}</p>
						<i class="fa-solid fa-bars"></i>
					</div>

					<transition name="slide">
						<div
							v-show="show"
							@click.self="show = false"
							class="fixed z-50 top-0 right-0 h-full bg-slate-800 text-white rounded-l-lg border-slate-300 border overflow-hidden w-64 flex flex-col pb-4"
						>
							<div class="p-4 flex-grow">
								<div class="flex justify-between items-center mb-4">
									<NavLink routeName="home" @click="show = false">
										<img
											src="/storage/app/public/images/listing/homelogo.svg"
											class="w-14"
										/>
									</NavLink>
									<button
										@click="show = false"
										class="text-white hover:text-gray-400 mr-2"
									>
										<i class="fa-solid fa-times p-2"></i>
									</button>
								</div>
								<div
									v-if="user.role === 'admin'"
									class="border-b border-gray-600 mb-4"
								></div>
								<div v-if="user.role === 'admin'" class="-mt-4">Admin</div>
								<NavLink
									v-if="user.role === 'admin'"
									:href="route('listing.create')"
									class="block w-full px-6 py-3 hover:bg-slate-700 text-left"
									@click="show = false"
								>
									New Listing
								</NavLink>
								<NavLink
									v-if="user.role === 'admin'"
									:href="route('dashboard')"
									class="block w-full px-6 py-3 hover:bg-slate-700 text-left"
									@click="show = false"
								>
									Dashboard
								</NavLink>
								<NavLink
									v-if="user.role === 'admin'"
									:href="route('admin.users')"
									class="block w-full px-6 py-3 hover:bg-slate-700 text-left"
									@click="show = false"
								>
									Users
								</NavLink>
								<NavLink
									v-if="user.role === 'admin'"
									:href="route('admin.adoption.applications')"
									class="block w-full px-6 py-3 hover:bg-slate-700 text-left"
									@click="show = false"
								>
									Applications
								</NavLink>
								<NavLink
									v-if="user.role === 'admin'"
									:href="route('admin.appointments')"
									class="block w-full px-6 py-3 hover:bg-slate-700 text-left"
									@click="show = false"
								>
									Appointments
								</NavLink>

								<br v-if="user.role === 'admin'" />
								<div class="border-b border-gray-600 mb-4"></div>
								<div v-if="user.role === 'admin'" class="-mt-4">Users</div>
								<NavLink
									:href="route('status')"
									class="block w-full px-6 py-3 hover:bg-slate-700 text-left"
									@click="show = false"
								>
									Requests
								</NavLink>
								<NavLink
									:href="route('profile.edit')"
									class="block w-full px-6 py-3 hover:bg-slate-700 text-left"
									@click="show = false"
								>
									Profile
								</NavLink>
							</div>
							<NavLink
								:href="route('logout')"
								method="post"
								as="button"
								class="block w-56 px-6 py-3 hover:bg-slate-700 text-left mt-4 ml-4"
								@click="show = false"
							>
								Logout
							</NavLink>
						</div>
					</transition>
				</div>

				<!-- Guest -->
				<div v-else class="space-x-6">
					<NavLink routeName="login" componentName="Auth/Login">Login</NavLink>
					<NavLink routeName="register" componentName="Auth/Register">Register</NavLink>
				</div>
			</div>
		</nav>
	</header>

	<main class="p-6 mx-auto w-full">
		<slot />
	</main>
</template>

<style scoped>
nav {
	display: flex;
	justify-content: space-between;
	align-items: center;
	width: 100%;
}
.notification-container {
	position: relative;
}
.notifications-list {
	scrollbar-width: thin;
	scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}
.notifications-list::-webkit-scrollbar {
	width: 6px;
}
.notifications-list::-webkit-scrollbar-track {
	background: transparent;
}
.notifications-list::-webkit-scrollbar-thumb {
	background-color: rgba(156, 163, 175, 0.5);
	border-radius: 3px;
}
.slide-enter-active,
.slide-leave-active {
	transition: transform 0.5s ease;
}
.slide-enter,
.slide-leave-to {
	transform: translateX(100%);
}
</style>
