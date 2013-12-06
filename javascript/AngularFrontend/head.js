<script src="{JAVASCRIPT_BASE}/AngularFrontend/angular.min.js"></script>

<script src="{JAVASCRIPT_BASE}/AngularFrontend/scripts/20bdd8fe.plugins.js"></script>
<script src="{JAVASCRIPT_BASE}/AngularFrontend/scripts/34dfa171.modules.js"></script>
<script src="{JAVASCRIPT_BASE}/AngularFrontend/scripts/c6b0052f.scripts.js"></script>

<script type="text/javascript">

	(function() {
		angular.module('roomDamages')
			.config(function(roomDamageBrokerProvider){
				roomDamageBrokerProvider.setDamageTypes({DAMAGE_TYPES});
				roomDamageBrokerProvider.setLocation('http://localhost/hms/phpwebsite/index.php');
			});
	})();
	
	(function() {
		angular.module('roomDamages')
			.config(function(roomDamageResidentProvider){
				roomDamageResidentProvider.setAssignment({ASSIGNMENT});
				roomDamageResidentProvider.setResidents({RESIDENTS});
				roomDamageResidentProvider.setStudent({STUDENT});
				roomDamageResidentProvider.setCheckin({CHECKIN});
			});
	})();
</script>