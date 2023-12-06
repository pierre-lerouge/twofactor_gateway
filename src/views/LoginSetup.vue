<template>
	<div>
		<div v-if="loading" class="loading"></div>
		<GatewaySettings gateway-name="sms" display-name="SMS" @confirm="confirm" />
		<form ref="confirmForm" method="POST"></form>
	</div>
</template>

<script>
	import GatewaySettings from "../components/LoginSetup.vue";
	import {tryVerification} from "../services/registration";
	export default {
		components: {
			GatewaySettings,
		},
		methods: {
			confirm() {
				this.$refs.confirmForm.submit()
			}	
		}
	}
</script>