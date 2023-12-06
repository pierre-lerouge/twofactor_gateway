<!--
  - @copyright 2019 Christoph Wurst <christoph@winzerhof-wurst.at>
  -
  - @author 2019 Christoph Wurst <christoph@winzerhof-wurst.at>
  -
  - @license GNU AGPL version 3 or any later version
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU Affero General Public License as
  - published by the Free Software Foundation, either version 3 of the
  - License, or (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU Affero General Public License for more details.
  -
  - You should have received a copy of the GNU Affero General Public License
  - along with this program.  If not, see <http://www.gnu.org/licenses/>.
  -->

<template>
	<div>
		<div v-if="!isAvailable">
			{{ t('twofactor_gateway', 'The {displayName} gateway is not configured.', {displayName: displayName}) }}
		</div>
		<div v-else-if="loading">
			<span class="icon-loading-small"></span>
		</div>
		<div v-else>
			<p v-if="state === 0">
				<slot name="instructions" />
				{{ t('twofactor_gateway', 'You are not using {displayName} for two-factor authentication at the moment.', {displayName: displayName}) }}
				<button @click="enable">
					{{ t('twofactor_gateway', 'Enable') }}
				</button>
			</p>
			<p v-if="state === 1">
				<slot name="instructions" />
				<strong v-if="verificationError === true">
					{{ t('twofactor_gateway', 'Could not verify your code. Please try again.') }}
				</strong>
				{{ t('twofactor_gateway', 'Enter your identification (e.g. phone number to start the verification):') }}
				<input v-model="identifier">
				<button @click="verify">
					{{ t('twofactor_gateway', 'Verify') }}
				</button>
			</p>
			<p v-if="state === 2">
				{{ t('twofactor_gateway', 'A confirmation code has been sent to {phone}. Please insert the code here:', {phone: phoneNumber}) }}
				<input v-model="confirmationCode">
				<button @click="confirm">
					{{ t('twofactor_gateway', 'Confirm') }}
				</button>
			</p>
			<p v-if="state === 3">
				{{ t('twofactor_gateway', 'Your account was successfully configured to receive messages via {displayName}.', {displayName: displayName}) }}
				<button @click="disable">
					{{ t('twofactor_gateway', 'Disable') }}
				</button>
			</p>
		</div>
	</div>
</template>

<script>
	import {
		getState,
		startVerification,
		tryVerification,
		disable
	} from "../services/registration";
	export default {
		name: "GatewaySettings",
		props: [
			'gatewayName',
			'displayName',
		],
		data () {
			return {
				loading: true,
				state: 0,
				isAvailable: true,
				phoneNumber: '',
				confirmationCode: '',
				identifier: '',
				verificationError: false
			};
		},
		mounted: function () {
			getState(this.gatewayName)
				.then(res => {
					console.debug('loaded state for gateway ' + this.gatewayName, res);
					this.isAvailable = res.isAvailable;
					this.state = res.state;
					this.phoneNumber = res.phoneNumber;
				})
				.catch(console.error.bind(this))
				.then(() => this.loading = false);
		},
		methods: {
			enable: function () {
				this.state = 1;
				this.verificationError = false;
				this.loading = false;
			},
			verify: function () {
				this.loading = true;
				this.verificationError = false;
				startVerification(this.gatewayName, this.identifier)
					.then(res => {
						this.state = 2;
						this.phoneNumber = res.phoneNumber;
						this.loading = false;
					})
					.catch(e => {
						console.error(e);
						this.state = 1;
						this.verificationError = true;
						this.loading = false;
					});
			},
			confirm: function () {
				this.loading = true;
				tryVerification(this.gatewayName, this.confirmationCode)
					.then(res => {
						this.state = 3;
						this.loading = false;
					})
					.catch(res => {
						this.state = 1;
						this.verificationError = true;
						this.loading = false;
					});
			},
			disable: function () {
				this.loading = true;
				disable(this.gatewayName)
					.then(res => {
						this.state = res.state;
						this.phoneNumber = res.phoneNumber;
						this.loading = false;
					})
					.catch(console.error.bind(this));
			}
		},
	}
</script>

<style>
	.icon-loading-small {
		padding-left: 15px;
	}
</style>