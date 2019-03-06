<template>
	<v-container grid-list-md>
		<v-layout row wrap>
			<v-flex xs12>
				<v-card elevation-1 class="data-card" dark>
					<v-layout row wrap>
						<v-flex xs12>
							<div class="card-title">
								Account Holder
							</div>
						</v-flex>
						<v-flex xs12>
							<v-form ref="user" v-model="validUser" class="custom-form">
								<v-container>
									<v-layout row wrap>
										<v-flex xs12 sm6 md4 lg4>
											<v-text-field id="name" v-model="profile.name" :rules="validation.name" label="Name"></v-text-field>
										</v-flex>

										<v-flex xs12 sm6 md8 lg8>
											<v-text-field id="surrname" v-model="profile.surrname" :rules="validation.surrnames" label="Surname(s)" ></v-text-field>
										</v-flex>

										<v-flex xs12 sm6 md4 lg4>
											<v-text-field id="address" v-model="profile.address" :rules="validation.address" label="Address"></v-text-field>
										</v-flex>

										<v-flex xs12 sm6 md4 lg4>
											<v-text-field id="postalcode" v-model="profile.postCode" :rules="validation.postCode" label="Post Code"></v-text-field>
										</v-flex>

										<v-flex xs12 sm6 md4 lg4>
											<v-text-field id="city" v-model="profile.city" :rules="validation.city" label="City"></v-text-field>
										</v-flex>

										<v-flex xs12 sm6 md4 lg4>
											<v-text-field id="country" v-model="profile.country" :rules="validation.country" label="Country"></v-text-field>
										</v-flex>

										<v-flex xs12 sm6 md8 lg8>
											<v-text-field id="iban" v-model="profile.iban" :rules="validation.iban" label="IBAN" ></v-text-field>
										</v-flex>

										<v-flex xs12 sm6 md4 lg4>
											<v-text-field id="id" v-model="profile.id" :rules="validation.id" label="ID"></v-text-field>
										</v-flex>

										<v-flex xs12 sm6 md4 lg4>
											<v-text-field id="phone" v-model="profile.telephone" :rules="validation.telephone" label="Telephone"></v-text-field>
										</v-flex>

										<v-flex xs12 sm6 md4 lg4>
											<v-menu
												:close-on-content-click="false"
												v-model="calendar"
												:nudge-right="40"
												lazy
												transition="scale-transition"
												offset-y
												full-width
												max-width="290px"
												min-width="290px"
											>
												<v-text-field
													id="birth"
													slot="activator"
													v-model="profile.birth"
													label="Date of birth"
													persistent-hint
													:rules="validation.age"
													readonly
												></v-text-field>
												<v-date-picker color="green lighten-1" v-model="profile.birth" no-title @input="calendar = false"></v-date-picker>
											</v-menu>
										</v-flex>

										<v-flex xs12 class="btn-next-container">
											<v-btn :disabled="!validUser" @click="dialog = !dialog" class="btn-green">Save</v-btn>
										</v-flex>
									</v-layout>
								</v-container>
							</v-form>
						</v-flex>					
					</v-layout>
				</v-card>
			</v-flex>
			<v-flex xs12>
				<v-card elevation-1 class="data-card" dark>
					<v-layout row wrap>
						<v-flex xs12>
							<div class="card-title">
								Business Information
							</div>
						</v-flex>
						<v-flex xs12>
							<v-form ref="business" v-model="validBusiness" class="custom-form">
								<v-container>
									<v-layout row wrap>
										<v-flex xs12 sm6 md4 lg4>
											<v-text-field id="company" :rules="validation.company" label="Company"></v-text-field>
										</v-flex>

										<v-flex xs12 sm6 md4 lg4>
											<v-text-field id="fiscalid" :rules="validation.fiscalId" label="Fiscal ID" ></v-text-field>
										</v-flex>

										<v-flex xs12 md4 lg4 class="btn-next-container">
											<v-btn :disabled="!validBusiness" @click="dialog = !dialog" class="btn-green">Save</v-btn>
										</v-flex>
									</v-layout>
								</v-container>
							</v-form>
						</v-flex>					
					</v-layout>
				</v-card>
			</v-flex>
			<v-flex xs12>
				<v-card elevation-1 class="data-card" dark>
					<v-layout row wrap>
						<v-flex xs12>
							<div class="card-title">
								Change Password
							</div>
						</v-flex>
						<v-flex xs12>
							<v-form ref="password" v-model="validPass" class="custom-form">
								<v-container>
									<v-layout row wrap>
										<v-flex xs12 sm6 md4 lg4>
											<v-text-field id="password" :rules="validation.password" v-model="profile.password" label="New Password"></v-text-field>
										</v-flex>

										<v-flex xs12 sm6 md4 lg4>
											<v-text-field id="oldpassword" :rules="validation.rePassword" v-model="profile.rePassword" label="Repeat Password" ></v-text-field>
										</v-flex>

										<v-flex xs12 md4 lg4 class="btn-next-container">
											<v-btn :disabled="!validPass" @click="dialog = !dialog" class="btn-green">Change</v-btn>
										</v-flex>
									</v-layout>
								</v-container>
							</v-form>
						</v-flex>					
					</v-layout>
				</v-card>
			</v-flex>				
		</v-layout>
		<v-dialog
			v-model="dialog"
			max-width="500px"
			transition="dialog-transition"
		>
			<v-card class="card-modal">
				<div class="title-modal text-xs-center">Your data has been saved succesfully!</div>
				<v-btn class="btn-green" @click="dialog = !dialog">Accept</v-btn>
			</v-card>
		</v-dialog>
	</v-container >
</template>
<script>
	export default {
		head () {
			return {
				title: 'Winvestify - Profile'
			}
		},
		data () {
			return {
				ex1: false,
				dialog: false,
				confirm: false,
				validUser: true,
				validBusiness: true,
				validPass: true,
				calendar: false,
				profile: {
					password: '',
					rePassword: ''
				},
				validation: {
					name: [
						v => !!v || 'Name is required',
						v => (v && v.length >= 2) || 'Name must be more than 2 characters',
						v => (v && v.length <= 50) || 'Name must be less than 50 characters'
					],
					surrnames: [
						v => !!v || 'Surrnames is required',
						v => (v && v.length >= 2) || 'Surrnames must be more than 2 characters',
						v => (v && v.length <= 50) || 'Surrnames must be less than 50 characters'
					],
					address: [
						v => !!v || 'Address is required',
						v => (v && v.length >= 2) || 'Address must be more than 2 characters',
						v => (v && v.length <= 60) || 'Address must be less than 60 characters'
					],
					postCode: [
						v => !!v || 'Postal Code is required',
						v => (v && v.length >= 2) || 'Postal Code must be more than 2 characters',
						v => (v && v.length <= 45) || 'Postal Code must be less than 45 characters'
					],
					city: [
						v => !!v || 'City is required',
						v => (v && v.length >= 2) || 'City must be more than 2 characters',
						v => (v && v.length <= 45) || 'City must be less than 45 characters'
					],
					country: [
						v => !!v || 'Country is required',
						v => (v && v.length >= 2) || 'Country must be more than 2 characters',
						v => (v && v.length <= 45) || 'Country must be less than 45 characters'
					],
					company: [
						v => !!v || 'Company is required',
						v => (v && v.length >= 2) || 'Company must be more than 2 characters'
					],
					fiscalId: [
						v => !!v || 'Fiscal ID is required',
						v => (v && v.length >= 2) || 'Fiscal ID must be more than 2 characters'
					],
					id: [
						v => !!v || 'Id is required',
						v => (v && v.length >= 3) || 'Id must be more than 3 characters',
						v => (v && v.length <= 20) || 'Id must be less than 20 characters'
					],
					telephone: [
						v => !!v || 'Telephone is required',
						v => (v && v.length >= 4) || 'Telephone must be more than 4 characters',
						v => (v && v.length <= 20) || 'Telephone must be less than 20 characters'
					],
					password: [
						v => !!v || 'Password is required',
						/* eslint-disable */
						v => /(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/.test(v) || 'Your password should be at least 8 characters long and contain uppercase and lowercase characters and a number and a symbol'
						/* eslint-enable */
					],
					rePassword: [
						v => !!v || 'Re-password is required',
						v => (v === this.profile.password) || 'Password and re-password field must be the same'
					],
					iban: [

					],
					age: [
						v => !!v || 'Date of Birth is required'
					]
				}
			}
		},
		mounted () {
			this.$store.commit('SET_BUTTON_BACK', false)
		},
		methods: {
		},
		components: {

		},
		layout: 'dashboard'
	}
</script>