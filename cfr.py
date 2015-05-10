#rewrite of the php class for python 3.4 (no support for 2.7)

import requests
import datetime
from lxml import objectify



global now
now = datetime.datetime.now()
global url_base 
url_base = 'http://www.gpo.gov/fdsys/bulkdata/CFR/'
global ttl
ttl = 3600
def build_url(title,vol,year = None):
	if year is None:
		year = str(now).split('-')[0]
	return(url_base + "title-" + year + "/CFR-" + year + "-title" + title + "-vol" + vol + ".xml")


def get_vol(title,vol,year = None):
	url = build_url(title,vol,year)
	data = fetch(url)
	return(url)
#	return(parse(data))


#doesnt work as per original author
def get_title(title,year = None):
	vol = 1
	while data == get_vol(title,year,vol):
		vol = vol + 1

def parse(data):
	return(objectify.fromstring(data))

def fetch(url):
#	if cache == get_cache(url):
#		return(cache)

	r = requests.get(url)

	if r.headers['content-type'] is not 'text/xml':
		return(False)

	data = r.content
	if (data is None) or (not data):
		return(False)

#	set_cache(url,data)

	return(data)

def get_cache(key):
	try:
		return(retrieve_cache(key))
	except:
		return(False)

def set_cache(key,value):
	try:
		return(insert_cache(key,value))
	except:
		return(False)



