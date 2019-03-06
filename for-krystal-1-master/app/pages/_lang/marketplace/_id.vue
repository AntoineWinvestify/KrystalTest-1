<template>
	<v-container grid-list-md>
		<!-- Layout title and lending select -->
    <v-layout row wrap>
      <v-flex xs12>
        <v-card elevation-1 class="data-card marketplace" dark>
					<v-layout row wrap>
						<v-flex xs12 sm4 md3 lg3>
							<v-autocomplete
								v-model="lending"
								:items="lendings"
								box
								content-class="select-active"
								placeholder="Select lending platform"
								item-text="name"
								item-value="name"
								hide-details
							/>
						</v-flex>
						<v-flex class="section-header" xs12 sm8 md9 lg9>
							<div class="controls">
								<div class="tabs">
									<div @click="selectTab(item)" :class="{'tab-active': item.selected}" v-for="(item) in tabs" :key="item.param" class="tab special">{{ item.title }}</div>
								</div>
								<figure>
									<img :src="company.company_logo_url" alt="">
								</figure>
							</div>
						</v-flex>
					</v-layout>
        </v-card>
      </v-flex>
		</v-layout>
    <v-layout row wrap>
      <v-flex xs12 sm12 md6 lg6>
        <v-card elevation-1 class="data-card marketplace information full-height" dark>
					<v-layout row wrap>
						<v-flex class="title-item" xs12>
							<i class="icon-win-layers"></i>
							<span>Key Information</span>
						</v-flex>
						<v-flex xs12>
							{{ company.company_key_info.company_key_info_text }}
						</v-flex>
						<v-flex class="information-platform" xs12>
							<v-layout row wrap>
								<v-flex class="item" xs6 sm3 md3 lg3>
									<img src="~/assets/img/lendings/mintos_logo.png" alt="">
									<span>Company Name</span>
								</v-flex>
								<v-flex class="item" xs6 sm3 md3 lg3>
									<span>{{ company.company_key_info.company_start }}</span>
									<span>Founded</span>
								</v-flex>
								<v-flex class="item" xs6 sm3 md3 lg3>
									<span>{{ company.company_key_info.is_regulated ? 'Yes' : 'No' }}</span>
									<span>Regulated</span>
								</v-flex>
								<v-flex class="item" xs6 sm3 md3 lg3>
									<span>{{ company.company_key_info.company_country_of_origin }}</span>
									<span>Country</span>
								</v-flex>
							</v-layout>
						</v-flex>
						<v-flex class="information-platform" xs12>
							<v-layout row wrap>
								<v-flex class="item item-two" xs12 sm6 md6 lg6>
									<span>
										<i class="icon-win-users"></i>
										+ {{ company.company_key_info.company_number_of_investors }}
									</span>
									<span>Registered Investors</span>
								</v-flex>
								<v-flex class="item" xs12 sm6 md6 lg6>
									<span>
										<i class="icon-win-bar-chart"></i>
										{{ company.company_key_info.company_total_invested_assets.amount.value | currency(company.company_key_info.company_total_invested_assets.amount.currency_code) }}
									</span>
									<span>Total Invested</span>
								</v-flex>
							</v-layout>
						</v-flex>						
					</v-layout>					
        </v-card>			
      </v-flex>
      <v-flex xs12 sm12 md6 lg6>
        <v-card elevation-1 class="data-card marketplace information full-height" dark>
					<v-layout row wrap>
						<v-flex class="title-item" xs12>
							<i class="icon-win-eye"></i>
							<span>Product Detail</span>
						</v-flex>
						<v-flex xs12>
							<v-layout row wrap>
								<indicator-item :amount="company.company_product_details.loan_types" name="Type loans"/>
								<indicator-item :amount="company.company_product_details.minimum_investment.value | currency(company.company_product_details.minimum_investment.currency_code)" name="Min. Investment"/>
								<indicator-item :amount="company.company_product_details.autoinvest_option ? 'Yes' : 'No'" name="AutoInvest"/>
							</v-layout>
						</v-flex>
						<v-flex xs12>
							<v-layout row wrap>
								<indicator-item :amount="company.company_product_details.fees ? 'Yes' : 'No'" name="Fee"/>
								<indicator-item :amount="company.company_product_details.secondary_market ? 'Yes' : 'No'" name="Secondary Market"/>
								<indicator-item :amount="setCurrencies(company.company_product_details.supported_currencies)" name="Currencies"/>
							</v-layout>
						</v-flex>
						<v-flex xs12>
							<v-layout row wrap>
								<indicator-item color="light-green accent-3" html amount='<i class="icon-win-stars"></i>' name="Top Lending Platform"/>
								<indicator-item color="light-green accent-3" html :amount="getIcon(company.company_product_details.buyback_guarantee)" name="Buyback Guarantee"/>
								<indicator-item color="light-green accent-3" html :amount="getIcon(company.company_product_details.collateral)" name="Collateral"/>
							</v-layout>
						</v-flex>											
					</v-layout>					
        </v-card>
      </v-flex>			
		</v-layout>
		<v-layout row wrap>
      <v-flex xs12 sm12 md6 lg6>
        <v-card elevation-1 class="data-card marketplace information" dark>
					<v-layout row wrap>
						<v-flex class="title-item" xs12>
							<i class="icon-win-cast"></i>
							<span>Offers</span>
						</v-flex>
					</v-layout>
        </v-card>
        <v-card elevation-1 class="second-card marketplace" dark>
					{{ company.company_commercial_offer_info.commercial_info_text }}
					<div class="btn-container">
						<v-btn :href="company.company_commercial_offer_info.commercial_offer_url" target="_blank">APPLY</v-btn>
					</div>					
        </v-card>
      </v-flex>
      <v-flex xs12 sm12 md6 lg6>
        <v-card elevation-1 class="data-card marketplace information" dark>
					<v-layout row wrap>
						<v-flex class="title-item" xs12>
							<i class="icon-win-credit-card"></i>
							<span>More info</span>
						</v-flex>
					</v-layout>
        </v-card>
        <v-card elevation-1 class="second-card marketplace ul-container" dark>
					<v-layout class="information-list" row wrap>
						<v-flex xs12>
							<v-layout class="item-container" row wrap>
								<v-flex class="information-item" xs5 sm4 md4 lg4 offset-sm3 offset-md2 offset-lg3>Custodial Funds:</v-flex>
								<v-flex xs7 sm5 md6 lg5>{{ company.company_extendedInfo.custodial_funds }}</v-flex>
							</v-layout>
							<v-layout class="item-container" row wrap>
								<v-flex class="information-item" xs5 sm4 md4 lg4 offset-sm3 offset-md2 offset-lg3>Transfer Funds:</v-flex>
								<v-flex xs7 sm5 md6 lg5>{{ company.company_extendedInfo.transfer_funds }}</v-flex>
							</v-layout>
							<v-layout class="item-container" row wrap>
								<v-flex class="information-item" xs5 sm4 md4 lg4 offset-sm3 offset-md2 offset-lg3>Withdrawals:</v-flex>
								<v-flex xs7 sm5 md6 lg5>{{ company.company_extendedInfo.withdrawals_fee ? 'Fee' : 'Fee Free' }}</v-flex>
							</v-layout>
							<v-layout class="item-container" row wrap>
								<v-flex class="information-item" xs5 sm4 md4 lg4 offset-sm3 offset-md2 offset-lg3>Secondary Market:</v-flex>
								<v-flex xs7 sm5 md6 lg5>{{ company.company_extendedInfo.secondary_market_fee ? 'Fee' : 'Fee Free' }}</v-flex>
							</v-layout>
							<v-layout class="item-container" row wrap>
								<v-flex class="information-item" xs5 sm4 md4 lg4 offset-sm3 offset-md2 offset-lg3>Sell-Out:</v-flex>
								<v-flex xs7 sm5 md6 lg5>{{ company.company_extendedInfo.sell_outs ? 'Yes' : 'No' }}</v-flex>
							</v-layout>
							<v-layout class="item-container" row wrap>
								<v-flex class="information-item" xs5 sm4 md4 lg4 offset-sm3 offset-md2 offset-lg3>Accepted Investors:</v-flex>
								<v-flex xs7 sm5 md6 lg5>{{ company.company_extendedInfo.accepted_investors }}</v-flex>
							</v-layout>
							<v-layout class="item-container" row wrap>
								<v-flex class="information-item" xs5 sm4 md4 lg4 offset-sm3 offset-md2 offset-lg3>Taxation:</v-flex>
								<v-flex xs7 sm5 md6 lg5>{{ company.company_extendedInfo.taxation }}</v-flex>
							</v-layout>																																														
						</v-flex>
					</v-layout>
        </v-card>				
      </v-flex>				
		</v-layout>
    <v-layout row wrap>
      <v-flex xs12>			
        <v-card elevation-1 class="data-card marketplace information" dark>
					<v-layout row wrap>
						<v-flex class="title-item" xs12>
							<v-icon>trending_up</v-icon>
							<span>Company Score</span>
						</v-flex>						
						<v-flex class="indicators-list-container" xs12 sm6 md4 lg5>
							<indicator v-if="indicators[0] && indicators[0].length > 0" v-for="(item, index) in indicators[0]" :key="index" :data="item" />
						</v-flex>
						<v-flex v-show="company && company.company_winvestify_evaluation && company.company_winvestify_evaluation.global_evaluation_actual_value" class="circle-indicator-container" xs12 sm12 md4 lg2 order-sm3 order-md2 order-lg2>
							<svg class="circle-chart__circle" viewbox="0 0 33.83098862 33.83098862" width="200" height="200" xmlns="http://www.w3.org/2000/svg">
								<defs>
									<linearGradient id="redyel" gradientUnits="objectBoundingBox" x1="0" y1="0" x2="1" y2="1">
											<stop offset="0%" stop-color="#1fff00"/>   
											<stop offset="100%" stop-color="#c4ff3a"/>   
									</linearGradient>
								</defs>
								<circle class="circle-chart__background" stroke="#efefef" stroke-width="1" fill="none" cx="16.91549431" cy="16.91549431" r="15.91549431" />
								<circle class="circle-chart__circle circle-chart__circle--negative" stroke="url(#redyel)" stroke-width="1" :stroke-dasharray="`${getPercent()},100`" stroke-linecap="" fill="none" cx="16.91549431" cy="16.91549431" r="15.91549431" />
								<g class="circle-chart__info">
									<text class="circle-chart-title" x="16.91549431" y="12.5" alignment-baseline="central" text-anchor="middle">Company Score</text>
									<text class="circle-chart-value" x="16.91549431" y="18.5" alignment-baseline="central" text-anchor="middle">{{ company.company_winvestify_evaluation.global_evaluation_actual_value }}</text>
								</g>
							</svg>
						</v-flex>
						<v-flex class="indicators-list-container" xs12 sm6 md4 lg5 order-sm2 order-md3 order-lg3>
							<indicator v-if="indicators[1] && indicators[1].length > 0" v-for="(item, index) in indicators[1]" :key="index" left :data="item" />
						</v-flex>									
					</v-layout>
        </v-card>
      </v-flex>
    </v-layout>
	</v-container>	
</template>
<script>
	import axios from '~/plugins/axios'
	import IndicatorContainer from '~/components/dashboard/IndicatorsContainer'
	import IndicatorItem from '~/components/dashboard/IndicatorItem'
	import Indicator from '~/components/marketplace/Indicator'
	export default {
		head () {
			return {
				title: 'Winvestify - Marketplace Information'
			}
		},
		data () {
			return {
				lending: {},
				lendings: [],
				company: {
					company_key_info: {
						company_total_invested_assets: {
							amount: {}
						}
					},
					company_product_details: {
						minimum_investment: {},
						supported_currencies: []
					},
					company_commercial_offer_info: {},
					company_extendedInfo: {},
					company_winvestify_evaluation: {}
				},
				indicators: [
					[], []
				],
				value: 840,
				max: 900,
				icon: '<i class="icon-win-stars"></i>',
				tabs: [
					{ title: 'Key info', param: 'default', selected: true },
					{ title: 'Statistics & Report', selected: false }
				]
			}
		},
		mounted () {
			this.$store.commit('SET_BUTTON_BACK', { link: `/${this.$store.state.locale}/marketplace` })
			this.getData()
		},
		methods: {
			getPercent () {
				return ((this.company.company_winvestify_evaluation.global_evaluation_actual_value * 100) / this.company.company_winvestify_evaluation.global_evaluation_max)
			},
			selectTab (tab) {
				this.tabs = this.tabs.map(item => {
					item.selected = false
					if (item.param === tab.param) {
						item.selected = true
					}
					return item
				})
			},
			getData () {
				axios.get(`/api/companies/marketplace/${this.$route.params.id}`)
					.then(({ data }) => {
						this.company = data
						this.setIndicators(this.company && this.company.company_winvestify_evaluation && this.company.company_winvestify_evaluation.details ? this.company.company_winvestify_evaluation.details : [])
					})
			},
			getIcon (value) {
				return value ? '<i class="icon-win-check-circle"></i>' : '<i class="icon-win-x-square"></i>'
			},
			setCurrencies (currencies) {
				const codes = currencies.map(item => {
					return item.currency_code
				})
				return codes.join(' / ')
			},
			setIndicators (indicators = []) {
				this.indicators = this.chunkArray(indicators, (indicators.length / 2))
			},
			chunkArray (indicators, chunk_size = 2) {
				let results = []

				while (indicators.length) {
					results.push(indicators.splice(0, chunk_size))
				}

				return results
			}
		},
		components: {
			IndicatorContainer,
			IndicatorItem,
			Indicator
		},
		layout: 'dashboard'
	}
</script>