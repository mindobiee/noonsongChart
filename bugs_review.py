from selenium import webdriver
from bs4 import BeautifulSoup
import pymysql
from selenium.webdriver.common.keys import Keys
import pandas as pd

crawling_num=0
comment_num=0

chromedriver_dir = r'C:\Users\user\Downloads\chromedriver_win32\chromedriver.exe'
driver = webdriver.Chrome(chromedriver_dir)
driver.maximize_window()
driver.implicitly_wait(5)

#create cursor
conn = pymysql.connect(host="127.0.0.1",
                       user ='root', passwd='960919', db="gradualDB" )
cur = conn.cursor()

# options to look like a human
options = webdriver.ChromeOptions()
options.add_argument("--headless")
options.add_argument("--no-sandbox")
options.add_argument("--disable-gpu")
options.add_argument("window-size=1920x1080")
options.add_argument("lang=ko_KR")
options.add_argument("user-agent=Chrome/77.0.3865.90")
driver.get('https://music.bugs.co.kr/chart')
source = driver.page_source
bs = BeautifulSoup(source, 'html.parser')

#Crawling comments :)
def crawlComments(bs):
    global crawling_num
    global comment_num
    #user, comment
    users = bs.find_all('span', class_="user")
    listComments = bs.find_all('div', class_="comment")
    comments = [i.find("p").text.strip() for i in listComments]
    users = [i.get_text().strip() for i in users]

    title = driver.find_element_by_xpath("/html/body/div[2]/div[2]/article/header/div/h1").text.strip()
    album = driver.find_element_by_xpath("/html/body/div[2]/div[2]/article/section[1]/div/div[1]/table/tbody/tr[3]/td/a").text.strip()

    #ReviewList table 데이터 삽입하기 (sql문)
    result = []
    for i in range(len(users)):
        query2 = """
        insert into reviewList values ("%s", "%s", "%s"); """ %(str(users[i]), str(comments[i+1]), str(title)+str(album))
        cur.execute(query2)
        result.append([users[i],comments[i+1]])

    conn.commit()
    df = pd.DataFrame(result, columns = ['users', 'comments'])
    df.to_excel("test.xlsx", encoding="utf-8")

def change_songs(song_num) :

    global comment_num
    xpath_songs = "/html/body/div[2]/div[2]/article/section/div/div[1]/table/tbody/tr["+str(song_num)+"]/td[4]/a"
    button_songs = driver.find_element_by_xpath(xpath_songs)
    button_songs.send_keys(Keys.ENTER)
    driver.implicitly_wait(10)

    # ReviewList 만들기
    query = "drop table if exists reviewList"
    cur.execute(query)
    query1 = """
            create table reviewList(
            user varchar(100),
            comment varchar(500),
            id varchar(100)
            );
        """
    cur.execute(query1)
    
    totalComment = driver.find_element_by_xpath("/html/body/div[2]/div[2]/article/section[6]/div/p[1]/span").text
    print('total number of comments :' + str(totalComment))

    source = driver.page_source
    bs = BeautifulSoup(source, 'html.parser')

    while (comment_num != totalComment):
        next_page = driver.find_element_by_xpath("/html/body/div[2]/div[2]/article/section[6]/div/p[4]/a")
        next_page.send_keys(Keys.ENTER)
        driver.implicitly_wait(5)
        comment_num = bs.find_all('span', class_="user").count()
        
    crawlComments(bs)
    print("crawling ends")

    # 한 곡의 댓글 모두 크롤링한 후 메인페이지로 돌아감.
    driver.get('https://music.bugs.co.kr/chart')
    source = driver.page_source

#메인 프로그램
if __name__ == '__main__':
    song_num = int(input("song_num :"))
    change_songs(song_num)
    #conn.commit()
    cur.close()
    conn.close()
    driver.quit()
