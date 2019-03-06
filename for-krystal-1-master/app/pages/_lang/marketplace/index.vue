<template>
	<v-container v-resize="resizing" grid-list-md>
		<v-layout row wrap>
			<v-flex xs12>
				<v-card elevation-1 class="data-card marketplace" dark>
					<v-layout row wrap>
						<v-flex xs12>
							<div class="card-title">
								Lending Platforms
							</div>
						</v-flex>
						<v-flex xs12>
							<v-layout :class="{ 'show': show }" class="filter-mobile" row wrap>
								<v-flex xs12>
									<v-layout row wrap>
										<v-flex class="title-check" md2 lg2>
											Type of Loans:
										</v-flex>
										<v-flex class="custom-checks main" md10 lg10>
											<v-checkbox
												v-model="ex1"
												label="Business Loans"
												color="light-green accent-3"
												value=""
												hide-details
											></v-checkbox>
											<v-checkbox
												v-model="ex2"
												label="Consumer Loans"
												color="light-green accent-3"
												value=""
												hide-details
											></v-checkbox>
											<v-checkbox
												v-model="ex3"
												label="Invoice Financing"
												color="light-green accent-3"
												value=""
												hide-details
											></v-checkbox>
											<v-checkbox
												v-model="ex4"
												label="Mortage Loans"
												color="light-green accent-3"
												value=""
												hide-details
											></v-checkbox>										
										</v-flex>
									</v-layout>
								</v-flex>
								<v-flex class="custom-checks main" xs12>
									<v-layout row wrap>
										<v-flex class="title-check" md2 lg2>
											Plus:
										</v-flex>
										<v-flex class="custom-checks main" md10 lg10>
											<v-checkbox
												v-model="ex6"
												label="Secondary Market"
												color="light-green accent-3"
												value=""
												hide-details
											></v-checkbox>
											<v-checkbox
												v-model="ex7"
												label="Cross-Border"
												color="light-green accent-3"
												value=""
												hide-details
											></v-checkbox>
											<v-checkbox
												v-model="ex8"
												label="Collateral"
												color="light-green accent-3"
												value=""
												hide-details
											></v-checkbox>
											<v-checkbox
												v-model="ex9"
												label="Buyback Guarantee"
												color="light-green accent-3"
												value=""
												hide-details
											></v-checkbox>										
										</v-flex>
									</v-layout>
								</v-flex>										
							</v-layout>
							<v-fab-transition>
								<v-btn
									class="filter-button"
									@click="show = !show"
									v-show="showFilter"
									color="blue accent-4"
									dark
									fixed
									small
									bottom
									right
									fab
								>
									<v-icon>settings</v-icon>
								</v-btn>
							</v-fab-transition>							
						</v-flex>					
					</v-layout>
				</v-card>
			</v-flex>
			<v-flex v-for="(item, index) in lendings" :key="index" xs12>
				<v-card elevation-1 class="data-card marketplace" dark>
					<v-layout class="lending-row" row wrap>
						<v-flex class="image-container" xs6 sm8 md2 lg2>
							<figure>
								<img :src="item.company_logo_url" alt="">
							</figure>
						</v-flex>
						<v-flex class="lendings-list" xs12 order-xs3 order-sm3 order-md2 order-lg2 md8 lg8>
							<div class="lendings-item">
								<span>All loans</span>
								<span>{{ item.company_product_details.loan_types }}</span>
							</div>
							<div class="lendings-item">
								<span>{{ item.company_product_details.minimum_investment.value | currency(item.company_product_details.minimum_investment.currency_code) }}</span>
								<span>Min. Investment</span>
							</div>
							<div class="lendings-item">
								<span>{{ item.company_product_details.crossborder ? 'Yes' : 'No' }}</span>
								<span>CrossBorder</span>
							</div>
							<div class="lendings-item">
								<span>{{ item.company_product_details.secondary_market ? 'Yes' : 'No' }}</span>
								<span>Secondary Market</span>
							</div>
							<div class="lendings-item">
								<span>{{ item.company_product_details.buyback_guarantee ? 'Yes' : 'No' }}</span>
								<span>Guarantee</span>
							</div>
							<div class="lendings-item">
								<span>{{ item.company_winvestify_evaluation.global_evaluation_actual_value }}</span>
								<span>FinScore</span>
							</div>																																			
						</v-flex>
						<v-flex class="btn-container" xs6 order-xs2 order-sm2 order-md3 sm4 md2 lg2>
							<v-btn nuxt :to="goTo(item.company_id)" color="success">Read More</v-btn>
						</v-flex>												
					</v-layout>
				</v-card>
			</v-flex>
		</v-layout>
	</v-container>
</template>

<script>
	import axios from '~/plugins/axios'

	export default {
		head () {
			return {
				title: 'Marketplace'
			}
		},
		data () {
			return {
				ex0: false,
				ex1: false,
				ex2: false,
				ex3: false,
				ex4: false,
				ex5: false,
				ex6: false,
				ex7: false,
				ex8: false,
				ex9: false,
				showFilter: false,
				show: false,
				lendings: []
			}
		},
		mounted () {
			this.$store.commit('SET_BUTTON_BACK', false)
			this.getData()
		},
		methods: {
			resizing () {
				if (window.innerWidth <= 800) {
					this.showFilter = true
				} else {
					this.showFilter = false
					this.show = false
				}
			},
			getData () {
				axios.get('/api/companies/marketplace')
					.then(({ data }) => {
						this.lendings = data
					})
			},
			goTo (id) {
				return `/${this.$store.state.locale}/marketplace/${id}`
			}
		},
		layout: 'dashboard'
	}
</script>