from bs4 import BeautifulSoup
import requests

from elasticsearch import Elasticsearch
from elasticsearch import helpers

class WebIndex():
    def __init__(self,parent=None):
            data=self.index_func()
            es = Elasticsearch('http://localhost:9200')
            helpers.bulk(es, data)

    def get_data(self):
        courses=[]
        with open("course.txt") as file:
            for link in file:
                url = link
                response = requests.get(url,timeout=70)
                content = BeautifulSoup(response.content, "html.parser")
                course={}
                tweet=content.find_all(class_='col-lg-12 col-md-12 col-sm-12 col-sm-12')[1]
                description=''
                if(tweet.find('h3')):
                    if(tweet.find_all(class_='yui-wk-div')):
                        for desc in tweet.find_all(class_='yui-wk-div'):
                            description= description + desc.get_text() +'\n'
                        course[tweet.find('h3').string]=description
                    else:
                        desc=tweet.find_all('div')
                        for i in range(0,len(desc)):
                            description= description +desc[i].get_text()+'\n'
                        course[tweet.find('h3').string]=description
                    
                cou=0
                key=''
                value=''
                for tweet in content.find_all('td'):
                    if(cou%2==0):
                        key=tweet.string
                    else:
                        if(tweet.find('li')):
                            for cate in tweet.find_all('li'):
                                value+=cate.string
                        else:
                            value=tweet.string
                        course[key]=value
                        key=''
                        value=''
                    cou+=1
                
                tweet=content.find('h1')
                course['title']=tweet.string
                course['link']=url
                tweet=content.find(class_='col-lg-12 col-md-12 col-sm-12 col-xs-12').string
                
                course['University']=tweet[tweet.find('|')+18:len(tweet)-11]
                course['Professor']=tweet[15:tweet.find('|')-6]
                tweet=content.find_all(class_='marginTop25')[0]
                if str(list(tweet.children)[0]).find('div')>=0:
                    for data in tweet.find_all('div'):
                        if(data.string==None):
                            if data.find_all('li'):
                                for data1 in data.find_all('li'):
                                    value+=data1.get_text()+'\n' 
                            else:
                                value+=data.get_text()+'\n'
                        else:
                            value+=data.get_text()+'\n'
                else:
                    value+=tweet.get_text()
                course['description']=value
                courses+=[course]
        return courses
    
    def index_func(self):
        courses=self.get_data()
        actions=[]
        for i in range(len(courses)):
            actions=actions+[
                                {
                                        "_index": "nptel_index_final",
                                        #"_type": "course",
                                        "_id": i,
                                        "_source": courses[i]
                                 }
                            ]
        return actions
    
    
    
    
WebIndex()