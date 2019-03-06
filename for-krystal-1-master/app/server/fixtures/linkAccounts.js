const accounts = {
	'tooltip_display_name': 'An linked account can have three states: <br>1 - QUEUED which means that the accountlinking event is put in a queue for collecting the data, <br< 2- ANALYZING which means that the all the collected data is being analyzed. This can take quite some time and depends on the size of the account. You will receive an SMS when this process has finished. <br> 3 - MONITORED, All data has been analyzed and is available in your dashboard. The account will from now on be regularly updated (typically daily)',
	'service_status': 'ACTIVE',
	'service_status_display_message': 'You are using the maximum number of linkedaccounts. If you like to link more accounts, please upgrade your subscription',
	'data': [
		{
			'id': 432455,
			'company_id': 25,
			'linkedaccount_status': 'ACTIVE',
			'linkedaccount_visual_state': 'ANALYZING',
			'linkedaccount_username': 'klaus.austria@gmail.com',
			'linkedaccount_password': 'myPassword',
			'linkedaccount_platform_display_name': 'klaus@gmail.com@EUR',
			'linkedaccount_alias': 'KLAUS',
			'links': [
				{
					'href': '/v1/linkedaccounts/432455',
					'rel': 'edit',
					'method': 'PATCH'
				},
				{
					'href': '/v1/linkedaccounts/432455',
					'rel': 'delete',
					'method': 'DELETE'
				},
				{
					'href': '/v1/pollingresources/499825',
					'rel': 'monitor',
					'method': 'GET'
				},
				{
					'href': '/v1/pollingresources/499825',
					'rel': 'delete',
					'method': 'DELETE'
				}
			]
		},
		{
			'id': 432456,
			'company_id': 25,
			'linkedaccount_status': 'ACTIVE',
			'linkedaccount_visual_state': 'MONITORED',
			'linkedaccount_username': 'antoine@gmail.com',
			'linkedaccount_password': 'myPassword',
			'linkedaccount_platform_display_name': 'klaus@gmail.com@USD',
			'linkedaccount_alias': 'KLAUS',
			'links': [
				{
					'href': '/v1/linkedaccounts/432456',
					'rel': 'edit',
					'method': 'PATCH'
				},
				{
					'href': '/v1/linkedaccounts/432456',
					'rel': 'delete',
					'method': 'DELETE'
				}
			]
		},
		{
			'id': 432458,
			'company_id': 26,
			'linkedaccount_status': 'ACTIVE',
			'linkedaccount_visual_state': 'MONITORED',
			'linkedaccount_username': 'antoine@gmail.com',
			'linkedaccount_password': 'myPassword',
			'linkedaccount_platform_display_name': 'kkukovetz@gmail.com',
			'linkedaccount_alias': 'KLAUS',
			'links': [
				{
					'href': '/v1/linkedaccounts/432458',
					'rel': 'edit',
					'method': 'PATCH'
				},
				{
					'href': '/v1/linkedaccounts/432458',
					'rel': 'delete',
					'method': 'DELETE'
				}
			]
		}
	]
}

const preChecks = {
	accounts: [
		{
			'account_identity': '154ded8e-52d2-48c8-8ba1-a39300b590a7',
			'account_platform_display_name': 'klaus@gmail.com'
		},
		{
			'account_identity': '13y4ed9x-52d2-48c8-8ba1-a39300b590a7',
			'account_platform_display_name': 'MLI Limited'
		}
	]
}

const created = {
  'data': {
    'feedback_message_user': 'This is the confirmation message for the end-user',
    'linkedaccount': {
      'id': 432455,
      'company_id': 25,
      'linkedaccount_username': 'klaus@gmail.com',
      'linkedaccount_password': 'myPassword',
      'linkedaccount_platform_display_name': 'klaus@gmail.com@eur',
      'linkedaccount_alias': 'KLAUS',
      'links': [
        {
          'href': '/v1/linkedaccounts/432455',
          'rel': 'edit',
          'method': 'PATCH'
        },
        {
          'href': '/v1/linkedaccounts/432455',
          'rel': 'delete',
          'method': 'DELETE'
        },
        {
          'href': '/v1/pollingresources/499825',
          'rel': 'monitor   ',
          'method': 'GET'
        },
        {
          'href': '/v1/pollingresources/499825',
          'rel': 'polling',
          'method': 'DELETE'
        }        
      ]
    }
  }
}

export {
	accounts,
	created,
	preChecks
}
