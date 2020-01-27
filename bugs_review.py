from datetime import time
from selenium import webdriver
from bs4 import BeautifulSoup
import pymysql
from selenium.common.exceptions import NoAlertPresentException
from selenium.webdriver.common.alert import Alert
crawling_num=0
comment_num=0

chromedriver_dir = r'C:\Users\user\Downloads\chromedriver_win32\chromedriver.exe'
driver = webdriver.Chrome(chromedriver_dir)

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
#driver.get('https://music.bugs.co.kr/track/31806631?wl_ref=list_tr_08_chart')
source = driver.page_source
bs = BeautifulSoup(source, 'html.parser')

#https://music.bugs.co.kr/track/31806631?wl_ref=list_tr_08_chart
# /html/body/div[2]/div[2]/article/section/div/div[1]/table/tbody/tr[1]/td[4]

#이전 한마디 댓글 페이지로 이동하는 함수
def change_pages() :

    global comment_num
    next_page = driver.find_element_by_xpath("/html/body/div[2]/div[2]/article/section[6]/div/p[4]/a")
    next_page.click()
    driver.implicitly_wait(5)

    #/html/body/div[2]/div[2]/article/section[6]/div/p[4]/a
    # <a href="javascript:;" class="btnMore" cmt_area="nextPageBtn">이전 한마디</a> ==$0
   #마지막 페이지일 경우 alert 제어




# 전체 댓글 수랑 비교하기!


#댓글 크롤링하는 함수
def crawlComments(bs):

    global crawling_num
    global comment_num
    #users, comments 저장하기
    users = bs.find_all('span', class_="user")
    listComments = bs.find_all('div', class_="comment")
    comments = [i.find("p").text.strip() for i in listComments]
    users = [i.get_text().strip() for i in users]

    #title, album 이름 조합해서 id만들기
    title = driver.find_element_by_xpath("/html/body/div[2]/div[2]/article/header/div/h1").text.strip()
    album = driver.find_element_by_xpath("/html/body/div[2]/div[2]/article/section[1]/div/div[1]/table/tbody/tr[3]/td/a").text.strip()

    crawling_num = crawling_num +1
    #print(" id = " + title + album);
    print(str(crawling_num)+"번째 크롤링")

    # comments도 첫번째 항목 제외하고 삽입할 것! ok

    # '''
    comment_num  = comment_num + len(users)
    print('Comment num :')
    print(comment_num)

    #사용자와 댓글 출력하는 조건문
    for i,user in enumerate(users) :
        print('%d : %s'%(i,user))

    for i,comment in enumerate(comments):
        print('%d : %s' % (i, comment))

    #'''
    #ReviewList table 데이터 삽입하기 (sql문)

    for i in range(len(users)):
        query2 = """
        insert into reviewList values ("%s", "%s", "%s"); """ %(str(users[i]), str(comments[i+1]), str(title)+str(album))
        cur.execute(query2)
    conn.commit()

#song_num를 인수로 받아서, 해당 곡에 대한 페이지를 보여주고, crawlComments 함수를 불러옴
def change_songs(song_num) :

    xpath_songs = "/html/body/div[2]/div[2]/article/section/div/div[1]/table/tbody/tr["+str(song_num)+"]/td[4]/a"
    button_songs = driver.find_element_by_xpath(xpath_songs)
    button_songs.click()
    driver.implicitly_wait(10)

    source = driver.page_source
    bs = BeautifulSoup(source, 'html.parser')

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
    # foreign key(id) references musicList(id)
    cur.execute(query1)

    # /html/body/div[2]/div[2]/article/section[6]/div/p[1]/span
    totalComment = driver.find_element_by_xpath("/html/body/div[2]/div[2]/article/section[6]/div/p[1]/span")
    # totalComment= bs.find(id="totalComment")
    print('total number of comments :')
    print(totalComment)

    crawlComments(bs)
    while (comment_num != totalComment):
        change_pages()
        source = driver.page_source
        bs = BeautifulSoup(source, 'html.parser')
        crawlComments(bs)
    print("crawling ends")

    # 한 곡의 댓글 모두 크롤링한 후 메인페이지로 돌아감.
    driver.get('https://music.bugs.co.kr/chart')
    source = driver.page_source

'''  
  crawlComments(bs)
    while(change_pages()):
        source = driver.page_source
        bs = BeautifulSoup(source,'html.parser')
        crawlComments(bs)
    print("crawling ends") 
    
'''

#메인 프로그램
if __name__ == '__main__':
    song_num = int(input("song_num :"))
    change_songs(song_num)

    #conn.commit()
    cur.close()
    conn.close()
    driver.quit()