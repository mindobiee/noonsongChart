from selenium import webdriver
from webdriver_manager.chrome import ChromeDriverManager
from bs4 import BeautifulSoup
from urllib.request import urlopen
import pymysql 

driver = webdriver.Chrome(ChromeDriverManager().install())

# create cursor

conn = pymysql.connect(host = "127.0.0.1",
                       user = 'root', passwd = '132365', db = 'gradproj')
cur = conn.cursor()

# options to look like a human
options = webdriver.ChromeOptions()
options.add_argument("--headless")
options.add_argument("--no-sandbox")
options.add_argument("--disable-gpu")

options.add_argument("window-size=1920x1080")
options.add_argument("lang=ko_KR")
options.add_argument("user-agent=Chrome/77.0.3865.90")

driver.get('https://genie.co.kr/chart/top200')
source = driver.page_source

bs = BeautifulSoup(source, 'html.parser')

def change_pages():
    xpath_pages = "/html/body/div[3]/div[2]/div/div/div[5]/div[2]/div[2]/a[5]"
    button_pages = driver.find_element_by_xpath(xpath_pages)
    button_pages.click()

    source = driver.page_source

    bs = BeautifulSoup(source, 'html.parser')

# def change_songs(song_num):
    
xpath_songs = "html/body/div[3]/div[2]/div[1]/div[6]/div/table/tbody/tr[" + str(1) + "]/td[4]/a"
button_songs = driver.find_element_by_xpath(xpath_songs)
button_songs.click()
driver.implicitly_wait(5)

source = driver.page_source
bs = BeautifulSoup(source, 'html.parser')

replies = bs.find_all(class_="reply-text")

writerId = [ i.find("strong").find("a").get_text() for i in replies ]
comment = [ i.find("p").get_text() for i in replies]

# 아이디로 중복제거 해야 함.



for i in range(0, len(writerId)):
    query = """
        insert into comments_ (writerId, comment) values ("%s", "%s")
    """%(str(writerId[i]), str(comment[i]) )
    
    cur.execute(query)

# driver.get('https://genie.co.kr/chart/top200')
# source = driver.page_source

# for i in range(1, 5):
#     change_songs(i)

conn.commit()
cur.close()
conn.close()
driver.quit() 