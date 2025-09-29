from panorama_local.modules import crm, simulation

# Create a CRM instance for testing
crm_instance = crm.CRM(db_path="./data/test_smoke.db")

print('CRM initial authenticated:', crm_instance.is_authenticated())
# register a user
ok = crm_instance.register('tester', 's3cr3t', 'Free')
print('Register tester:', ok)
print('Authenticated after register:', crm_instance.is_authenticated())
# simulate login/logout
crm_instance.logout()
print('Authenticated after logout:', crm_instance.is_authenticated())
ok = crm_instance.login('tester', 's3cr3t')
print('Login tester:', ok)
print('Authenticated after login:', crm_instance.is_authenticated())
# run a simulation
pmt = {'markers': [{'id':1},{'id':2},{'id':3}], 'plan': 'Free'}
res = simulation.run_simulation(pmt, engine='cityflow')
print('Simulation result keys:', list(res.keys()))
# save project
crm_instance.add_project('tester', pmt)
projects = crm_instance.list_projects('tester')
print('Projects count for tester:', len(projects))
print('Smoke test completed successfully')
