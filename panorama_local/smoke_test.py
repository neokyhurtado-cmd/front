from panorama_local.modules import crm, simulation

print('CRM initial authenticated:', crm.global_crm.is_authenticated())
# register a user
ok = crm.global_crm.register('tester', 's3cr3t', 'Free')
print('Register tester:', ok)
print('Authenticated after register:', crm.global_crm.is_authenticated())
# simulate login/logout
crm.global_crm.logout()
print('Authenticated after logout:', crm.global_crm.is_authenticated())
ok = crm.global_crm.login('tester', 's3cr3t')
print('Login tester:', ok)
print('Authenticated after login:', crm.global_crm.is_authenticated())
# run a simulation
pmt = {'markers': [{'id':1},{'id':2},{'id':3}], 'plan': 'Free'}
res = simulation.run_simulation(pmt, engine='cityflow')
print('Simulation result keys:', list(res.keys()))
# save project
crm.global_crm.add_project('tester', pmt)
projects = crm.global_crm.list_projects('tester')
print('Projects count for tester:', len(projects))
print('Smoke test completed successfully')
