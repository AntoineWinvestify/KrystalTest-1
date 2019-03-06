<template>
	<v-container class="dashboard-modal-container modal-responsive" v-if="newUser">
		<div class="dashboard-modal-content" row nowrap>
			<div>
				<figure class="modal-image-container">
					<img src="~/assets/img/panel_modal.png" alt="">
				</figure>
			</div>
			<div class="modal-message">
				<div>
					<figure class="modal-logo-container">
						<img src="~/assets/img/logo-text.png" alt="">
					</figure>
				</div>
				<p>Winvestify is at the forefront of this opportunity with analytics solutions and expert services that help our  investor navigate the complexities of the Online Lending market to maximize the value of their assets and portfolios</p>
				<p>Connect with many of the largest and most influential P2P Lending platforms. We empower your decisions with software and data analytics solutions that provide data-driven insights regarding their P2P loans.</p>

				<v-btn nuxt :to="`/${$store.state.locale}/link`" block>Link Accounts</v-btn>
			</div>
		</div>
		<v-dialog
			v-model="confirm"
			max-width="500px"
			persistent
			transition="dialog-transition"
		>
			<v-card class="card-modal">
				<div class="title-modal-confirm">Before further...</div>
				<p>We need you accept our <a class="link-terms" href="#">terms of service</a> and the <a class="link-terms" href="#">privacy policy</a></p>
				<p>Clicking on the accept you are accepting</p>
				<p><b>Do you accept terms of service and the privacy policy?</b></p>
				<div class="buttons">
					<v-btn nuxt to="/">Cancel</v-btn>
					<v-btn class="btn-green" @click="confirm = !confirm, loadDash()">Confirm</v-btn>
				</div>
			</v-card>
		</v-dialog>	
	</v-container>
	<v-container v-else grid-list-md>
		<!-- Layout title and lending select -->
    <v-layout row wrap>
      <v-flex xs12>
        <v-card elevation-1 class="data-card" dark>
          <v-card-text class="flex-8" >{{ $t('dashboard.title') }}</v-card-text>
					<v-autocomplete
						v-model="lending"
						:items="lendings"
						box
						content-class="select-active"
						placeholder="Select Account"
						item-text="name"
						item-value="name"
						return-object
						@change="goLending"
						hide-details
					/>					
        </v-card>
      </v-flex>
		</v-layout>
		<!-- Layout chart and lending table -->
    <v-layout row wrap>
      <v-flex xs12 md6 lg6>
        <v-card elevation-1 class="data-card" dark>
					<v-layout column wrap>
						<v-flex xs12>
							<v-card-text>Evolution Yield</v-card-text>
						</v-flex>
						<v-flex class="tab-graph-container" xs12>
							<span @click="selectTab(item)" :class="{'tab-active': item.selected}" v-for="(item) in tabs" :key="item.param">{{ item.title }}</span>
						</v-flex>						
						<v-flex xs12 class="item-graph main general">
							<chart theme="ovilia-green" :options="option" ref="area" auto-resize></chart>
						</v-flex>
					</v-layout>
        </v-card>
      </v-flex>
			<v-flex xs12 md6 lg6>
				<general-indicator-layout>
					<template slot="left">
						<indicator-container name="Statement of Funds">
							<template slot>
								<indicator-item
									:amount="dashboard.invested_assets.value.amount | currency(dashboard.invested_assets.value.currency_code)"
									:amountIcon="!!dashboard.invested_assets.icon"
									:infoIcon="!!dashboard.invested_assets.tooltip_display_name"
									:name="dashboard.invested_assets.display_name"
									:tooltipContent="dashboard.invested_assets.tooltip_display_name"/>
								<indicator-item
									:amount="dashboard.reservedFunds.value.amount | currency(dashboard.reservedFunds.value.currency_code)"
									:amountIcon="!!dashboard.reservedFunds.icon"
									:infoIcon="!!dashboard.reservedFunds.tooltip_display_name"
									:name="dashboard.reservedFunds.display_name"
									:tooltipContent="dashboard.reservedFunds.tooltip_display_name"/>
								<indicator-item
									:amount="dashboard.cash.value.amount | currency(dashboard.cash.value.currency_code)"
									:amountIcon="!!dashboard.cash.icon"
									:infoIcon="!!dashboard.cash.tooltip_display_name"
									:name="dashboard.cash.display_name"
									:tooltipContent="dashboard.cash.tooltip_display_name"/>
							</template>
						</indicator-container>					
					</template>
					<template slot="rigth">
						<indicator-container :iconInfo="true" name="Net Annual Returns">
							<template slot>
								<indicator-item amount="10.31%" name="Last 365 days" :amountIcon="!!true"/>
								<indicator-item amount="10.31%" name="Last year"/>
								<indicator-item amount="9.85%" name="Total Funds"/>
							</template>
						</indicator-container>								
					</template>							
				</general-indicator-layout>
			</v-flex>
		</v-layout>
		<v-layout row wrap>
      <v-flex xs12>
        <v-card elevation-1 class="data-card" dark>
					<v-layout column wrap>
						<!-- Layout chart -->
						<v-flex class="section-title" xs12>
							<div class="title-item">
								<v-card-text>{{ dashboard.kpis.display_name }}</v-card-text>
								<tooltip small>
									<template slot="content">
										<div>
											<p>{{ dashboard.kpis.tooltip_display_name }}</p>
										</div>										
									</template>
								</tooltip>
							</div>
							<v-btn icon>
								<v-icon>more_vert</v-icon>
							</v-btn>
						</v-flex>
						<v-flex xs12>
							<v-data-table
								:headers="dashboard.kpis.table_header"
								:items="dashboard.kpis.table_data"
								hide-actions
								class="resume-table"
							>
							<template slot="headerCell" slot-scope="props">
								{{ props.header.display_name }}
								<tooltip v-if="props.header.tooltip_display_name" tableSmall small>
									<template slot="content">
										<div>
											<p>{{ props.header.tooltip_display_name }}</p>
										</div>										
									</template>
								</tooltip>
							</template>							
								<template slot="items" slot-scope="props">
									<td :class="{ 'tr-red': props.item[0].issue_text }" @click="goLending(props.item[0].datum)" class="text-xs-left">{{ props.item[0].datum }} ({{ props.item[1].datum }})
										<tooltip v-if="props.item[0].issue_text" tableSmall small>
											<template slot="content">
												<div>
													<p>{{ props.item[0].issue_text }}</p>
												</div>										
											</template>
										</tooltip>
									</td>
									<td 
										v-for="(item, index) in props.item.slice(2)" 
										:key="index" 
										:class="{ 'tr-red': props.item[0].issue_text }" 
										@click="goLending(props.item[0].datum)" 
										class="text-xs-center">
											{{ item.datum && item.datum.amount | currency(item.datum.currency_code) }}
											{{ item.datum && item.datum.percent && `${item.datum.percent}%` }}
										</td>
								</template>
							</v-data-table>
						</v-flex>
					</v-layout>
        </v-card>
      </v-flex>				
		</v-layout>
		<v-layout row wrap>
      <v-flex xs12 sm12 md6 lg6>
        <v-card elevation-1 class="data-card" dark>
					<v-layout column wrap>
						<v-flex class="section-title" xs12>
							<div class="title-item">
								<v-card-text>Net Returns</v-card-text>
								<tooltip small>
									<template slot="content">
										<div>
											<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel architecto saepe debitis error labore aspernatur, ex at tenetur, maiores asperiores, assumenda fugit. Fuga veritatis officia commodi ut impedit tempore in.</p>
										</div>										
									</template>
								</tooltip>
							</div>
							<v-btn icon>
								<v-icon>more_vert</v-icon>
							</v-btn>
						</v-flex>		
						<v-flex xs12 class="item-graph second">
							<chart theme="ovilia-green" :options="optionArea" ref="area2" auto-resize></chart>
						</v-flex>
					</v-layout>
        </v-card>
      </v-flex>
			<v-flex xs12 sm12 md6 lg6>
				<general-indicator-layout>
					<template slot="left">
						<indicator-container name="Investment Indicators">
							<template slot>
								<indicator-item
									amount="65.325"
									:amountIcon="!!dashboard.active_investments.icon"
									:infoIcon="!!dashboard.active_investments.tooltip_display_name"
									:name="dashboard.active_investments.display_name"
									:tooltipContent="dashboard.active_investments.tooltip_display_name"/>
								<indicator-item
									amount="698.430"
									:amountIcon="!!dashboard.net_deposits.icon"
									:infoIcon="!!dashboard.net_deposits.tooltip_display_name"
									:name="dashboard.net_deposits.display_name"
									:tooltipContent="dashboard.net_deposits.tooltip_display_name"/>
								<indicator-item 
									amount="9.85%"
									:amountIcon="!!dashboard.cashDrag.icon"
									:infoIcon="!!dashboard.cashDrag.tooltip_display_name"
									:name="dashboard.cashDrag.display_name"
									:tooltipContent="dashboard.cashDrag.tooltip_display_name"/>
							</template>
						</indicator-container>					
					</template>
					<template slot="rigth">
						<indicator-container :iconInfo="true" name="Net Earnings">
							<template slot>
								<indicator-item color="light-green accent-3" :amount="'12798' | currency" name="Last 365 days"/>
								<indicator-item color="light-green accent-3" :amount="'12202' | currency" name="Last year"/>
								<indicator-item color="light-green accent-3" :amount="'25000' | currency" name="Total Funds"/>
							</template>
						</indicator-container>								
					</template>				
				</general-indicator-layout>
			</v-flex>	
		</v-layout>
    <v-layout row wrap>
      <v-flex xs12 sm7 md7 lg6>
        <v-card elevation-1 class="data-card" dark>
					<v-layout column wrap>
						<v-flex class="section-title" xs12>
							<div class="title-item">
								<v-card-text>Payment Delay</v-card-text>
								<tooltip small>
									<template slot="content">
										<div>
											<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel architecto saepe debitis error labore aspernatur, ex at tenetur, maiores asperiores, assumenda fugit. Fuga veritatis officia commodi ut impedit tempore in.</p>
										</div>										
									</template>
								</tooltip>
							</div>
							<v-btn icon>
								<v-icon>more_vert</v-icon>
							</v-btn>
						</v-flex>		
						<v-flex xs12 class="item-graph second pie-chart">
							<v-layout row wrap>
								<v-flex xs12 sm8 md8>
									<chart theme="ovilia-green" :options="pie" ref="pie" auto-resize></chart>
								</v-flex>
								<v-flex class="data-list-container" xs12 sm4 md4>
									<div class="data-list-title">More than 91 days</div>
									<div class="data-list">
										<div class="data-list-item">
											<span>0.21%</span>
											<v-tooltip :content-class="`winvestify-tooltip ${small ? 'small' : ''}`" bottom>
												<template slot="activator">
													<span class="tooltip">Delinquency Rates</span>					
												</template>
												<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nemo minus tempore quis nesciunt non facere illum nihil quibusdam expedita. Iure eius deserunt quam? Sint expedita labore voluptate, explicabo sapiente ea?</p>
											</v-tooltip>
										</div>
										<div class="data-list-item">
											<span>{{'122.21' | currency}}</span>
											<v-tooltip :content-class="`winvestify-tooltip ${small ? 'small' : ''}`" bottom>
												<template slot="activator">
													<span class="tooltip">Outstanding debts</span>		
												</template>
												<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nemo minus tempore quis nesciunt non facere illum nihil quibusdam expedita. Iure eius deserunt quam? Sint expedita labore voluptate, explicabo sapiente ea?</p>
											</v-tooltip>											
										</div>
										<div class="data-list-item">
											<span>{{'1312.13' | currency}}</span>
											<v-tooltip :content-class="`winvestify-tooltip ${small ? 'small' : ''}`" bottom>
												<template slot="activator">
													<span class="tooltip">Written Off</span>
												</template>
												<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nemo minus tempore quis nesciunt non facere illum nihil quibusdam expedita. Iure eius deserunt quam? Sint expedita labore voluptate, explicabo sapiente ea?</p>
											</v-tooltip>														
										</div>																					
									</div>
								</v-flex>
							</v-layout>
						</v-flex>
					</v-layout>
        </v-card>
      </v-flex>
      <v-flex xs12 sm5 md5 lg6>
        <v-card elevation-1 class="data-card not-center" dark>
					<v-layout column wrap>
						<v-flex class="section-title" xs12>
							<div class="title-item">
								<v-card-text>Current</v-card-text>
								<tooltip small>
									<template slot="content">
										<div>
											<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel architecto saepe debitis error labore aspernatur, ex at tenetur, maiores asperiores, assumenda fugit. Fuga veritatis officia commodi ut impedit tempore in.</p>
										</div>										
									</template>
								</tooltip>
							</div>
							<v-btn icon>
								<v-icon>more_vert</v-icon>
							</v-btn>
						</v-flex>
						<v-flex class="gauge-chart" xs12>
							<!-- <thermometer title="Global Daily Current" :value="85.55" /> -->
							<div v-if="showLegend" class="chart-info">
								<span>{{ current.percent }}</span>
								<span>{{ current.displayName }}</span>
							</div>
							<chart theme="ovilia-green" :options="optionGauge" ref="gauge" auto-resize></chart>
						</v-flex>
					</v-layout>
        </v-card>
      </v-flex>				
		</v-layout>		
	</v-container>
</template>

<script>
import axios from '~/plugins/axios'

/* UI Components */
import IndicatorLayout from '~/components/dashboard/IndicatorsLayout'
import GeneralIndicatorLayout from '~/components/dashboard/GeneralIndicatorsLayout'
import IndicatorContainer from '~/components/dashboard/IndicatorsContainer'
import IndicatorItem from '~/components/dashboard/IndicatorItem'
import Thermometer from '~/components/dashboard/Thermometer'
import Tooltip from '~/components/ui/Tooltip'

/* CHART ELEMENTS */
import ECharts from 'vue-echarts/components/ECharts.vue'
import { option, data, optionArea, dataArea } from '~/fixtures/charts/area'
import { optionGauge, dataGauge } from '~/fixtures/charts/gauge'
import { pie, dataPie } from '~/fixtures/charts/pie'
import theme from '~/fixtures/theme.json'
import 'echarts/lib/chart/bar'
import 'echarts/lib/chart/gauge'
import 'echarts/theme/dark'
ECharts.registerTheme('ovilia-green', theme)

export default {
	async asyncData () {
		let data = await axios.get('/api/dashboard')
			.then(({ data }) => {
				return data
			})

		return { dashboard: data }
	},
	head () {
		return {
			title: 'Winvestify - Dashboard'
		}
	},
	data () {
		return {
			lending: {},
			optionGauge,
			current: {},
			confirm: true,
			newUser: true,
			dataGauge,
			small: true,
			optionArea,
			dataArea,
			option,
			pie,
			showLegend: false,
			lendings: [
				{ id: 'mintos', name: 'Mintos', username: 'Username 1', value: false, yield: '11.3%', totalVolume: '225000', cash: '5563.42', exposure: '28.125%', current: '98.21%', issue: false },
				{ id: 'estateGuru', name: 'EstateGuru', username: 'Username 2', value: false, yield: '9.88%', totalVolume: '175000', cash: '3321.38', exposure: '21.875%', current: '93.7%', issue: true },
				{ id: 'bondora', name: 'Bondora', username: 'Username 3', value: false, yield: '9.76%', totalVolume: '130000', cash: '2314.93', exposure: '16.25%', current: '50.5%', issue: false },
				{ id: 'twino', name: 'Twino', username: 'Username 4', value: false, yield: '8.21%', totalVolume: '100000', cash: '978.77', exposure: '12.50%', current: '47.1%', issue: true },
				{ id: 'finanzarel', name: 'Finanzarel', username: 'Username 5', value: false, yield: '6.65%', totalVolume: '60000', cash: '687.33', exposure: '7.50%', current: '39.5%', issue: false }
			],
			tabs: [
				{ title: 'Last Year', param: 'year', selected: true },
				{ title: 'All', param: 'all', selected: false }
			],
			headers: [
				{ tooltip: false, text: 'Platform (Username)', align: 'left', sortable: false, value: 'name' },
				{ tooltip: false, text: 'Yield', align: 'center', value: 'yield' },
				{ tooltip: true, text: 'Total Volume', align: 'center', value: 'totalVolume' },
				{ tooltip: true, text: 'Cash', align: 'center', value: 'cash' },
				{ tooltip: true, text: 'Exposure', align: 'center', value: 'exposure' },
				{ tooltip: true, text: 'Current', align: 'center', value: 'current' }
			]
		}
	},
	mounted () {
		this.$store.commit('SET_BUTTON_BACK', false)
		// this.findAndSetIssues()
	},
	methods: {
		loadDash () {
			this.newUser = false
			setTimeout(() => {
				this.loadCharts()
				this.getDataCharts()
			}, 3000)
		},
		loadCharts () {
			if (!this.newUser) {
				let area1 = this.$refs.area
				let area2 = this.$refs.area2
				let gauge = this.$refs.gauge
				let pie = this.$refs.pie

				pie.showLoading({
					text: 'Loading chart information ...',
					textColor: '#FFFFFF',
					color: '#73DC50',
					maskColor: '#393E44'
				})
				gauge.showLoading({
					text: 'Loading chart information ...',
					textColor: '#FFFFFF',
					color: '#73DC50',
					maskColor: '#393E44'
				})
				area1.showLoading({
					text: 'Loading chart information ...',
					textColor: '#FFFFFF',
					color: '#73DC50',
					maskColor: '#393E44'
				})
				setTimeout(() => {
					area1.hideLoading()
					area1.mergeOptions(data)
				}, 1500)
				area2.showLoading({
					text: 'Loading chart information ...',
					textColor: '#FFFFFF',
					color: '#73DC50',
					maskColor: '#393E44'
				})
				setTimeout(() => {
					area2.hideLoading()
					area2.mergeOptions(dataArea)
				}, 1500)
			}
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
		goLending (item = null) {
			console.log(item)
			let param = item
			if (item) {
				param = item
			}
			this.$router.push({
				name: 'lang-dashboard-id',
				params: {
					lang: this.$store.state.locale,
					id: param
				}
			})
		},
		setIssue (platform) {
			this.issue = this.dashboard.kpis.issues.find(item => {
				return item.issue_platform === platform
			})
		},
		getDataCharts () {
			this.getCurrentChart()
			this.getPaymentChart()
		},
		getCurrentChart () {
			let gauge = this.$refs.gauge

			axios.get('/api/dashboard/chart/current')
				.then(({ data }) => {
					const { max_value, percent } = data.dataset_1.data[0]
					// console.log(data)
					gauge.hideLoading()
					gauge.mergeOptions(dataGauge(percent, max_value))
					this.current = {
						percent: `${percent}%`,
						displayName: data.dataset_1.display_name
					}
					this.showLegend = !this.showLegend
				})
		},
		getPaymentChart () {
			let pie = this.$refs.pie

			axios.get('/api/dashboard/chart/payment')
				.then(({ data }) => {
					pie.hideLoading()
					pie.mergeOptions(dataPie(data.dataset1))
				})
		}
	},
	components: {
		chart: ECharts,
		IndicatorLayout,
		GeneralIndicatorLayout,
		IndicatorContainer,
		IndicatorItem,
		Thermometer,
		Tooltip
	},
	layout: 'dashboard'
}
</script>