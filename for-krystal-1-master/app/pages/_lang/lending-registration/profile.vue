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
							<v-form ref="lending" v-model="valid" class="custom-form">
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
											<v-text-field id="postalcode" v-model="profile.postCode" :rules="validation.postCode" label="Postal Code"></v-text-field>
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
											<v-text-field id="phone" v-model="profile.telephone" disabled="" :rules="validation.telephone" label="Telephone"></v-text-field>
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

										<v-flex class="custom-checks" xs12>
											<v-checkbox v-model="ex1" label="I use my company as an investment vehicle" color="light-blue darken-3" value="" hide-details></v-checkbox>
										</v-flex>

										<template v-if="ex1">
											<v-flex xs12 sm6>
												<v-text-field id="company" :rules="validation.company" label="Company"></v-text-field>
											</v-flex>
											<v-flex xs12 sm6>
												<v-text-field id="fiscalid" :rules="validation.fiscalId" label="Fiscal ID" ></v-text-field>
											</v-flex>																		
										</template>
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
								Upload Documents
							</div>
						</v-flex>
						<v-flex xs12 class="table-actions-info">
							<v-data-table
								:headers="headers"
								:items="data"
								hide-actions
							>
								<template slot="headerCell" slot-scope="props">
									<div class="header-cell">
										<i :class="props.header.icon" class="icon-table"></i>
										{{ props.header.text }}											
									</div>
								</template>
								<template slot="items" slot-scope="props">
									<td class="text-xs-left">
										{{ props.item.title }}
										<tooltip tableSmall small>
											<template slot="content">
												<div>
													<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel architecto saepe debitis error labore aspernatur, ex at tenetur, maiores asperiores, assumenda fugit. Fuga veritatis officia commodi ut impedit tempore in.</p>
												</div>										
											</template>
										</tooltip>										
									</td>
									<td class="text-xs-left">
										<v-btn @click="upload(props.item)" :class="{ 'uploaded': props.item.status === 1 }">{{ props.item.status === 1 ? 'Uploaded' : 'Upload' }}</v-btn>
									</td>
									<td class="text-xs-left">
										<div v-if="props.item.status === 1" class="td-actions">
											<i class="icon-win-trash"></i> Delete
										</div>
									</td>
									<td class="text-xs-left">
										<div :class="{ 'ready' : props.item.verified, 'pending' : !props.item.verified }" class="td-actions">
											<v-icon class="point">fiber_manual_record</v-icon>{{ props.item.verified ? 'Correct' : 'Pending' }}
										</div>
									</td>
								</template>
							</v-data-table>								
						</v-flex>
						<v-flex xs12 class="btn-next-container">
							<v-btn >Cancel</v-btn>
							<v-btn :disabled="!valid" class="btn-green">Send</v-btn>
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
				<div class="title-modal">Your request has been processed succesfully!</div>
				<p>We will proceed to send your information to the platforms requested to start the registration process. 
					Once verified and accepted you will be notified</p>
					<v-btn class="btn-green" @click="dialog = !dialog">Finish</v-btn>
			</v-card>
		</v-dialog>

		<v-dialog
			v-model="confirm"
			max-width="500px"
			persistent
			transition="dialog-transition"
		>
			<v-card class="card-modal">
				<div class="title-modal-confirm">Before further...</div>
				<p>The data completed in this form along with the documents uploaded by you will be sent to the selected platform (s).</p>
				<p>Before finalizing this process,</p>
				<p><b>Do you authorize Winvestify to send all the necessary data to complete your correct registration process?</b></p>
				<div class="buttons">
					<v-btn nuxt :to="`/${$store.state.locale}/lending-registration`" >Cancel</v-btn>
					<v-btn class="btn-green" @click="confirm = !confirm">Confirm</v-btn>
				</div>
			</v-card>
		</v-dialog>		
	</v-container >
</template>
<script>
	import Tooltip from '~/components/ui/Tooltip'
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
				calendar: false,
				valid: true,
				profile: {
					telephone: '1121212'
				},
				headers: [
					{ text: 'Document', align: 'center', sortable: false, icon: 'icon-win-pocket' },
					{ text: 'Status', align: 'center', sortable: false, icon: 'icon-win-user-check' },
					{ text: 'Action', align: 'center', sortable: false, icon: 'icon-win-settings' },
					{ text: 'Verified', align: 'center', sortable: false, icon: 'icon-win-cpu' }
				],
				data: [
					{ title: 'ID Card Front', status: 1, verified: true },
					{ title: 'ID Card Back', status: 1, verified: true },
					{ title: 'Bank N° ID', status: 0, verified: false }
				],
				validation: {
					name: [
						v => !!v || 'Name is required',
						v => (v && v.length >= 2) || 'Name must be more than 2 characters'
					],
					surrnames: [
						v => !!v || 'Surrnames is required',
						v => (v && v.length >= 2) || 'Surrnames must be more than 2 characters'
					],
					address: [
						v => !!v || 'Address is required',
						v => (v && v.length >= 2) || 'Address must be more than 2 characters'
					],
					postCode: [
						v => !!v || 'Postal Code is required',
						v => (v && v.length >= 2) || 'Postal Code must be more than 2 characters'
					],
					city: [
						v => !!v || 'City is required',
						v => (v && v.length >= 2) || 'City must be more than 2 characters'
					],
					country: [
						v => !!v || 'Country is required',
						v => (v && v.length >= 2) || 'Country must be more than 2 characters'
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
						v => (v && v.length >= 3) || 'Id must be more than 3 characters'
					],
					telephone: [
						v => !!v || 'Telephone is required',
						v => (v && v.length >= 4) || 'Telephone must be more than 4 characters'
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
			this.confirm = true
			this.$store.commit('SET_BUTTON_BACK', { link: `/${this.$store.state.locale}/lending-registration` })
		},
		methods: {
			upload (value) {
				if (value.status === 0) {
					this.dialog = true
					this.data = this.data.map(item => {
						if (item.title === value.title) {
							item.status = 1
							item.verified = true
						}
						return item
					})
				}
			}
		},
		components: {
			Tooltip
		},
		layout: 'dashboard'
	}
</script>