<template>
  <v-container grid-list-xs>
		<v-card elevation-1 class="platform-card">
			<div class="py-3 px-3 controls">
				<div class="tabs">
					<div @click="selectTab(item)" :class="{'tab-active': item.selected}" v-for="(item) in tabs" :key="item.param" class="tab">{{ item.title }}</div>
				</div>
				<figure>
					<img src="~/assets/img/lendings/mintos_logo.png" alt="">
				</figure>
			</div>
			<v-data-table
				:headers="headers"
				:items="info"
				:search="search"
				:pagination.sync="pagination"
				hide-actions
			>
				<template slot="headerCell" slot-scope="props">
					{{ props.header.text }}
					<tooltip v-if="props.header.tooltip" table small>
						<template slot="content">
							<div>
								<p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Vel architecto saepe debitis error labore aspernatur, ex at tenetur, maiores asperiores, assumenda fugit. Fuga veritatis officia commodi ut impedit tempore in.</p>
							</div>										
						</template>
					</tooltip>
				</template>
				<template slot="items" slot-scope="props">
					<td class="text-xs-left">{{ props.item.id }}</td>
					<td class="text-xs-center">{{ props.item.date }}</td>
					<td class="text-xs-center">{{ props.item.investment | currency }}</td>
					<td class="text-xs-center">{{ props.item.interes }}%</td>
					<td class="text-xs-center">{{ props.item.progress }}%</td>
					<td class="text-xs-center">{{ props.item.outstanding | currency }}</td>
					<td class="text-xs-center">{{ props.item.paymentDate }}</td>
					<td class="text-xs-center">{{ props.item.status }}</td>
				</template>
			</v-data-table>
			<div class="text-xs-center">
				<div class="pagination-info" v-if="pagination.totalItems == null">
					No entries to count
				</div>				
				<div class="pagination-info" v-else-if="pagination.page === 1">
					Showing {{ pagination.page }} - {{ pagination.rowsPerPage }} of {{ pagination.totalItems }} entries
				</div>
				<div class="pagination-info" v-else>
					Showing {{ ((pagination.page - 1) * pagination.rowsPerPage) + 1 }} - {{ (pagination.rowsPerPage * pagination.page) }} of {{ pagination.totalItems }} entries
				</div>				
				<v-pagination prev-icon="arrow_left" next-icon="arrow_right" v-model="pagination.page" :length="pages"></v-pagination>
			</div>
		</v-card>
	</v-container>
</template>
<script>
	/* DATA */
	import dataTable from '~/fixtures/dataTable'
	import Tooltip from '~/components/ui/Tooltip'

	export default {
		head () {
			return {
				title: 'Winvestify - Platforms'
			}
		},
		data () {
			return {
				search: '',
				pagination: {
					rowsPerPage: 10
				},
				selected: [],
				headers: [
					{ text: 'Loan ID', align: 'left', sortable: false, value: 'id' },
					{ text: 'Investment Date', align: 'center', value: 'date' },
					{ text: 'My Investment', align: 'center', value: 'investment' },
					{ text: 'Interes', align: 'center', value: 'interes' },
					{ tooltip: true, text: 'Instalment Progress', align: 'center', value: 'progress' },
					{ tooltip: true, text: 'Outstanding Principal', align: 'center', value: 'outstanding' },
					{ text: 'Next Payment', align: 'center', value: 'paymentDate' },
					{ text: 'Status', align: 'center', value: 'status' }
				],
				info: dataTable(),
				tabs: [
					{ title: 'Default', param: 'default', selected: true },
					{ title: 'Active Investments', param: 'active_investments', selected: false },
					{ title: 'Duplicity', param: 'duplicity', selected: false }
				]
			}
		},
		mounted () {
			console.log(this.backLink)
			this.$store.commit('SET_BUTTON_BACK', { link: this.backLink && this.backLink.indexOf('dashboard/') >= 0 ? this.backLink : `${this.$store.state.locale}/dashboard` })
		},
		computed: {
			backLink () {
				return this.$store.getters.backLink
			},
			pages () {
				if (this.pagination.rowsPerPage == null ||
					this.pagination.totalItems == null
				) return 0

				return Math.ceil(this.pagination.totalItems / this.pagination.rowsPerPage)
			}
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
			}
		},
		components: {
			Tooltip
		},
		layout: 'dashboard'
	}
</script>