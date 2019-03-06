<template>
	<v-container grid-list-md>
		<!-- Layout title and lending select -->
    <v-layout row wrap>
      <v-flex xs12>
        <v-card elevation-1 class="data-card platform-card dashboard-lending" dark>
					<v-layout row wrap>
						<v-flex xs12 sm12 md3 lg3>
							<v-autocomplete
								v-model="lending"
								:items="lendings"
								box
								content-class="select-active"
								placeholder="Select Account"
								item-text="name"
								item-value="name"
								hide-details
							/>
						</v-flex>
						<v-flex class="section-header" xs12 sm12 md9 lg9>
							<div class="tabs">
								<div class="tab">
									<nuxt-link :to="`/${$store.state.locale}/dashboard/platforms`">My investment list</nuxt-link>
									</div>
							</div>
							<div class="controls">
								<span class="account-name">Account name</span>
								<div class="lending-image-container">
									<figure>
										<img src="~/assets/img/lendings/mintos_logo.png" alt="">
									</figure>
									<v-tooltip content-class="winvestify-tooltip" bottom>
										<v-icon
											slot="activator"
										>
											add_circle_outline
										</v-icon>
										<div>
											<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel architecto saepe debitis error labore aspernatur, ex at tenetur, maiores asperiores, assumenda fugit. Fuga veritatis officia commodi ut impedit tempore in.</p>
											<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. At, impedit? Necessitatibus illo nesciunt exercitationem velit debitis aperiam ipsa alias dolor optio, ut maxime, commodi reiciendis iusto recusandae ex vero. Debitis. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Sunt saepe nostrum a eum quibusdam sequi dicta porro ipsam id, culpa totam dolor, alias quisquam atque incidunt possimus, sed ea numquam?</p>
											<p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Recusandae animi atque nostrum temporibus placeat aliquid corrupti alias et aperiam repudiandae hic tempora, id culpa. Blanditiis iusto voluptatum odio numquam distinctio.</p>
											<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Possimus vel quod aut harum sint provident vero eligendi ea enim impedit illum consequuntur totam quidem, earum cumque recusandae cum nulla alias. Lorem ipsum dolor sit amet consectetur adipisicing elit. Id, saepe labore ea tenetur tempore voluptatum corrupti numquam, ducimus distinctio provident nulla, sequi cum. Amet, delectus. Soluta officia non nam consequuntur!</p>
										</div>
									</v-tooltip>
								</div>
							</div>
						</v-flex>						
					</v-layout>
        </v-card>
      </v-flex>
		</v-layout>
		<!-- Layout chart and lending table -->
    <v-layout row wrap>
      <v-flex xs12>
        <v-card elevation-1 class="data-card" dark>
					<v-layout column wrap>
						<v-flex xs12>
							<v-card-text>Evolution Yield</v-card-text>
						</v-flex>
						<v-flex class="tab-graph-container" xs12>
							<span @click="selectTab(item)" :class="{'tab-active': item.selected}" v-for="(item) in tabs" :key="item.param">{{ item.title }}</span>
						</v-flex>						
						<v-flex xs12 class="item-graph main">
							<chart theme="ovilia-green" :options="option" ref="area" auto-resize></chart>
						</v-flex>
					</v-layout>
        </v-card>
      </v-flex>		
		</v-layout>
		<indicator-layout>
			<template slot="left">
				<indicator-container name="Investment Indicators">
					<template slot>
						<indicator-item amount="65.325" name="Active Investments" :amountIcon="true"/>
						<indicator-item :amount="'1698430' | currency" name="Net Deposits" :amountIcon="true" :infoIcon="true"/>
						<indicator-item amount="9.85%" name="Cash Drag" :amountIcon="true" :infoIcon="true"/>
					</template>
				</indicator-container>					
			</template>
			<template slot="rigth">
				<indicator-container name="Statement of Funds">
					<template slot>
						<indicator-item :amount="'800000' | currency" name="Invested Assets" :infoIcon="true"/>
						<indicator-item :amount="'23495' | currency" name="Reserved Funds" :infoIcon="true"/>
						<indicator-item :amount="'13496' | currency" name="Cash" :infoIcon="true"/>
					</template>
				</indicator-container>								
			</template>				
		</indicator-layout>
		<indicator-layout>
			<template slot="left">
				<indicator-container name="Net Annual Returns" :iconInfo="true">
					<template slot>
						<indicator-item amount="10.31%" name="Last 365 days" :infoIcon="true" :amountIcon="true"/>
						<indicator-item amount="10.05%" name="Last year" :infoIcon="true"/>
						<indicator-item amount="9.85%" name="Total funds" :infoIcon="true"/>
					</template>
				</indicator-container>					
			</template>
			<template slot="rigth">
				<indicator-container :iconInfo="true" name="Net Earnings">
					<template slot>
						<indicator-item color="success" :amount="'12798' | currency" name="Last 365 days"/>
						<indicator-item color="success" :amount="'12202' | currency" name="Last year"/>
						<indicator-item color="success" :amount="'25000' | currency" name="Total Funds"/>
					</template>
				</indicator-container>								
			</template>				
		</indicator-layout>
    <v-layout row wrap>
      <v-flex xs12 md6 lg6>
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
      <v-flex xs12 md6 lg6>
				<v-layout justify-space-around row wrap justify-center>
					<v-flex xs12 sm6 md6 lg6>
						<v-card elevation-1 class="data-card" dark>
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
					<v-flex xs12 sm6 md6 lg6>	
						<v-card elevation-1 class="data-card" dark>
							<v-layout column wrap>
								<v-flex class="section-title" xs12>
									<div class="title-item">
										<v-card-text>Exposure</v-card-text>
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
										<span>{{ exposure.percent }}</span>
										<span>{{ exposure.displayName }}</span>
									</div>
									<chart theme="ovilia-green" :options="optionGauge" ref="gauge2" auto-resize></chart>
								</v-flex>
							</v-layout>
						</v-card>					
					</v-flex>
				</v-layout>
      </v-flex>
		</v-layout>		
	</v-container>
</template>

<script>
import axios from '~/plugins/axios'

/* UI Components */
import IndicatorLayout from '~/components/dashboard/IndicatorsLayout'
import IndicatorContainer from '~/components/dashboard/IndicatorsContainer'
import IndicatorItem from '~/components/dashboard/IndicatorItem'
import Thermometer from '~/components/dashboard/Thermometer'
import Tooltip from '~/components/ui/Tooltip'

/* CHART ELEMENTS */
import ECharts from 'vue-echarts/components/ECharts.vue'
import { option, data } from '~/fixtures/charts/line'
import { optionGauge, dataGauge } from '~/fixtures/charts/gauge'
import { pie, dataPie } from '~/fixtures/charts/pie'
import theme from '~/fixtures/theme.json'
import 'echarts/lib/chart/bar'
import 'echarts/theme/dark'
ECharts.registerTheme('ovilia-green', theme)

export default {
	head () {
		return {
			title: 'Winvestify - Lending (Mintos)'
		}
	},
	data () {
		return {
			lending: '',
			option,
			optionGauge,
			showLegend: false,
			dataGauge,
			small: true,
			pie,
			current: {},
			exposure: {},
			lendings: [
				{ name: 'Mintos (kukovetz@mli-ltd.com)', value: false, yield: '11.3%', totalVolumen: '225000', cash: '5563.42', exposure: '28.125%', current: '98.21%' },
				{ name: 'EstateGuru', value: false, yield: '9.88%', totalVolumen: '175000', cash: '3321.38', exposure: '21.875%', current: '93.7%' },
				{ name: 'Bondora', value: false, yield: '9.76%', totalVolumen: '130000', cash: '2314.93', exposure: '16.25%', current: '50.5%' },
				{ name: 'Twino', value: false, yield: '8.21%', totalVolumen: '100000', cash: '978.77', exposure: '12.50%', current: '47.1%' },
				{ name: 'Finanzarel', value: false, yield: '6.65%', totalVolumen: '60000', cash: '687.33', exposure: '7.50%', current: '39.5%' }
			],
			tabs: [
				{ title: 'Last Year', param: 'year', selected: true },
				{ title: 'All', param: 'all', selected: false }
			],
			headers: [
				{ text: 'Platform', align: 'center', sortable: false, value: 'name' },
				{ text: 'Yield', align: 'center', value: 'yield' },
				{ text: 'Total Volumen', align: 'center', value: 'totalVolumen' },
				{ text: 'Cash', align: 'center', value: 'cash' },
				{ text: 'Exposure', align: 'center', value: 'exposure' },
				{ text: 'Current', align: 'center', value: 'current' }
			]
		}
	},
	mounted () {
		this.$store.commit('SET_BUTTON_BACK', { link: `/${this.$store.state.locale}/dashboard` })
		let area1 = this.$refs.area
		let gauge = this.$refs.gauge
		let gauge2 = this.$refs.gauge2
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
		gauge2.showLoading({
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
		this.getDataCharts()
		setTimeout(() => {
			area1.hideLoading()
			area1.mergeOptions(data)
			this.showLegend = !this.showLegend
		}, 1500)
	},
	methods: {
		selectTab (tab) {
			this.tabs = this.tabs.map(item => {
				item.selected = false
				if (item.param === tab.param) {
					item.selected = true
				}
				return item
			})
		},
		goPlatforms () {
			this.$router.push({
				name: 'dashboard-platforms'
			})
		},
		getDataCharts () {
			this.getCurrentChart()
			this.getExposureChart()
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
					this.showLegend = true
				})
		},
		getExposureChart () {
			let gauge = this.$refs.gauge2

			axios.get('/api/dashboard/chart/exposure')
				.then(({ data }) => {
					const { max_value, percent } = data.dataset_1.data[0]
					// console.log(data)
					gauge.hideLoading()
					gauge.mergeOptions(dataGauge(percent, max_value, 2))
					this.exposure = {
						percent: `${percent}%`,
						displayName: data.dataset_1.display_name
					}
					this.showLegend = true
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
		IndicatorContainer,
		IndicatorItem,
		Thermometer,
		Tooltip
	},
	layout: 'dashboard'
}
</script>
<style lang="stylus" scoped>

</style>
