<template>
	<v-container grid-list-md>
		<v-layout row wrap>
			<v-flex xs12>
				<v-card elevation-1 class="data-card link-account" dark>
					<v-layout row wrap>
						<v-flex xs12>
							<div class="card-title">
								Link Account
							</div>
						</v-flex>
						<v-flex xs12>
							<v-form ref="link" v-model="valid" class="custom-form">
								<v-container>
									<v-layout row wrap>
										<v-flex class="flex-container" xs12 sm4 md3 lg3>
											<v-autocomplete
												id="platform"
												v-model="account.lending"
												:items="lendings"
												box
												content-class="select-active"
												placeholder="Select lending platform"
												item-text="name"
												item-value="id"
												:rules="validation.select"
											/>
										</v-flex>

										<v-flex xs12 sm4 md3 lg3>
											<v-text-field id="username" :rules="validation.usernameRules" v-model="account.user" label="Username" ></v-text-field>
										</v-flex>

										<v-flex xs12 sm4 md3 lg3>
											<v-text-field id="password" :rules="validation.passwordRules" v-model="account.password" type="password" label="Password"></v-text-field>
										</v-flex>

										<v-flex class="flex-container" offset-xs2 xs8 offset-sm4 offset-md0 sm4 md3 lg3>
											<v-btn :disabled="!valid" @click="preCheck">Link Account</v-btn>
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
								Linked Accounts
								<tooltip small>
									<template slot="content">
										<div>
											<p v-html="info.tooltip_display_name"></p>
										</div>										
									</template>
								</tooltip>								
							</div>
						</v-flex>
						<v-flex xs12 class="table-actions-info">
							<v-data-table
								:headers="headers"
								:items="info.data"
								hide-actions
							>
								<template slot="headerCell" slot-scope="props">
									<div v-if="!props.header.tooltip" class="header-cell">
										<i :class="props.header.icon" class="icon-table"></i>
										{{ props.header.text }}											
									</div>
									<div v-else class="header-cell">
										<v-tooltip :content-class="`winvestify-tooltip ${small ? 'small' : ''}`" bottom>
											<template slot="activator">
												<i :class="props.header.icon" class="show-icon icon-table"></i>
												{{ props.header.text }}
											</template>
											<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatum consequatur magnam fuga repudiandae ducimus nisi reprehenderit labore perspiciatis ad vitae dolores inventore, accusantium quidem optio. Itaque temporibus eaque dolore distinctio?</p>
										</v-tooltip>																				
									</div>									
								</template>

								<template slot="items" slot-scope="props">
									<td class="text-xs-left td-actions">
										<img :src="props.item.src" alt="">{{ props.item.company_id }}
									</td>
									<td class="text-xs-left">
										{{ props.item.linkedaccount_platform_display_name }}
									</td>
									<td class="text-xs-left">{{ props.item.linkedaccount_password }}</td>
									<td class="text-xs-left">{{ props.item.connection }}</td>
									<td class="text-xs-left">
										<div :class="{ 'ready' : props.item.linkedaccount_visual_state === 'MONITORED', 'pending' : props.item.linkedaccount_visual_state === 'ANALYZING', 'waiting' : props.item.linkedaccount_visual_state === 'QUEUE' }" class="td-actions">
											<v-icon class="point">fiber_manual_record</v-icon>
											{{ props.item.linkedaccount_visual_state }}
										</div>
									</td>
									<td class="text-xs-left">
										<div class="td-actions">
											<i class="icon-win-trash"></i>
											<i class="icon-win-edit"></i>
										</div>
									</td>										
								</template>
							</v-data-table>								
						</v-flex>
					</v-layout>
				</v-card>
			</v-flex>			
		</v-layout>
		<v-dialog
			v-model="dialog"
			max-width="500px"
			persistent
			transition="dialog-transition"
		>
			<v-card class="card-modal custom-checks">
				<div class="title-modal-confirm">Select your investment account</div>
					<v-checkbox
						v-model="selectedAccounts"
						:label="item.account_platform_display_name"
						color="light-blue accent-3"
						:value="item.account_identity"
						hide-details
						v-for="item in accounts" :key="item.account_identity"
					></v-checkbox>
				<div class="buttons">
					<v-btn @click="dialog = !dialog">Cancel</v-btn>
					<v-btn class="btn-green" @click="linkAccount">Confirm</v-btn>
				</div>
			</v-card>
		</v-dialog>
		<v-dialog
			v-model="confirm"
			max-width="500px"
			transition="dialog-transition"
		>
			<v-card class="card-modal">
				<div class="title-modal">Your request has been processed succesfully!</div>
				<p>We will proceed to send your information to the platforms requested to start the registration process. 
					Once verified and accepted you will be notified</p>
					<v-btn class="btn-green" @click="confirm = !confirm">Finish</v-btn>
			</v-card>
		</v-dialog>		
	</v-container >
</template>
<script>
	import axios from '~/plugins/axios'
	
	import Tooltip from '~/components/ui/Tooltip'
	
	export default {
		async asyncData () {
			let data = await axios.get('/api/link')
				.then(({ data }) => {
					return data
				})

			return { info: data }
		},
		head () {
			return {
				title: 'Winvestify - Link account'
			}
		},
		data () {
			return {
				selectedAccounts: [],
				dialog: false,
				small: true,
				valid: true,
				confirm: false,
				account: {
					lending: null,
					user: '',
					password: ''
				},
				accounts: [],
				headers: [
					{ text: 'Lending Platforms', align: 'center', sortable: false, icon: 'icon-win-pocket' },
					{ text: 'User', align: 'center', sortable: false, icon: 'icon-win-user-check' },
					{ text: 'Password', align: 'center', sortable: false, icon: 'icon-win-lock' },
					{ text: 'Connection', align: 'center', sortable: false, icon: 'icon-win-shield' },
					{ text: 'Status', align: 'center', sortable: false, icon: 'icon-win-cpu', tooltip: true },
					{ text: 'Settings', align: 'center', sortable: false, icon: 'icon-win-settings', tooltip: true }
				],
				data: [
				],
				lendings: [
					{ id: 1, name: 'Mintos', src: '/lendings/mintos_logo.png' },
					{ id: 2, name: 'Twino', src: '/lendings/twino.png' },
					{ id: 3, name: 'Estateguru', src: '/lendings/estateguru.png' },
					{ id: 4, name: 'Bondora', src: '/lendings/bondora_logo.png' },
					{ id: 5, name: 'Felow Finance', src: '/lendings/felow_finance_logo.png' },
					{ id: 6, name: 'Circulantis', src: '/lendings/circulantis_invert.png' },
					{ id: 7, name: 'Loanbook', src: '/lendings/loanbook_logo.png' },
					{ id: 8, name: 'Finanzarel', src: '/lendings/finanzarel_logo_white.png' },
					{ id: 9, name: 'Ecrowd', src: '/lendings/ecrowd_logo.png' }
				],
				validation: {
					passwordRules: [
						v => !!v || 'Password is required',
						v => (v && v.length >= 5) || 'Password must be more than 5 characters'
					],
					usernameRules: [
						v => !!v || 'Username is required',
						v => (v && v.length >= 2) || 'Username must be more than 2 characters'
					],
					select: [
						v => !!v || 'Platform is required'
					]
				}
			}
		},
		mounted () {
			// this.confirm = true
			this.$store.commit('SET_BUTTON_BACK', false)
		},
		methods: {
			preCheck () {
				axios.post('/api/link/pre-check', {
					companyId: this.account.lending,
					username: this.account.user,
					password: this.account.password
				})
					.then(({ data }) => {
						if (data.accounts.length > 0) {
							this.accounts = data.accounts
							this.dialog = !this.dialog
						}
					})
			},
			linkAccount () {
				/* this.info.data.push({ src: this.account.lending.src, user: this.account.user, connection: 'Encrypted', status: 'Queue' })
				this.analizeAccount(this.account.user) */
				axios.post('/api/link', {
					companyId: this.account.lending,
					username: this.account.user,
					password: this.account.password,
					linkedAccount: {
						username: '',
						account: '',
						alias: ''
					}
				})
					.then(({ data }) => {
						this.dialog = false
						this.confirm = true
						this.clear()
					})
			},
			analizeAccount (user) {
				setTimeout(() => {
					this.info.data = this.data.map(item => {
						if (item.user === user) {
							item.status = 'Analyzing'
						}
						return item
					})
					this.acceptAccount(user)
				}, 5000)
			},
			acceptAccount (user) {
				setTimeout(() => {
					this.data = this.data.map(item => {
						if (item.user === user) {
							item.status = 'Monitored'
						}
						return item
					})
				}, 5000)
			},
			clear () {
				this.$refs.link.reset()
			}
		},
		components: {
			Tooltip
		},
		layout: 'dashboard'
	}
</script>