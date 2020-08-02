"""
api登陆B站
"""

import base64
import hashlib
import random
import string
from configparser import ConfigParser
from urllib import parse

import requests
import rsa


class BilibiliLogin:
    """
    api登录
    """
    ua = '''
    Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36
    '''

    def __init__(self):
        config = ConfigParser()
        config.read('.config', encoding='UTF-8')
        user = config['user']
        self.username, self.password = user['username'], user['password']
        self.cookie = ""
        self.access_token = ""

    def post(self, url, data=None, headers=None, json=None, decode=True, timeout=10):
        '''
        发送post请求
        '''
        try:
            response = requests.post(
                url, data=data, headers=headers, json=json, timeout=timeout
            )
            return response.json() if decode else response.content
        except:
            return None

    def get(self, url, headers=None, decode=True, timeout=10):
        '''
        发送get请求
        '''
        try:
            response = requests.get(url, headers=headers, timeout=timeout)
            return response.json() if decode else response.content
        except:
            return None

    def getSign(self, param):
        '''
        获取签名
        '''
        salt = "560c52ccd288fed045859ed18bffd973"
        signHash = hashlib.md5()
        signHash.update(f"{param}{salt}".encode())
        return signHash.hexdigest()

    def login(self):
        '''
        登录
        '''
        appKey = "1d8b6e7d45233436"
        url = "https://passport.bilibili.com/api/oauth2/getKey"
        data = {"appkey": appKey, "sign": self.getSign(f"appkey={appKey}")}
        response = self.post(url, data=data)
        if response and response.get("code") == 0:
            keyHash = response["data"]["hash"]
            pubKey = rsa.PublicKey.load_pkcs1_openssl_pem(
                response["data"]["key"].encode()
            )
        else:
            print(f"Key获取失败 {response}")
            return False
        url = "https://passport.bilibili.com/api/v2/oauth2/login"
        param = f"appkey={appKey}&password={parse.quote_plus(base64.b64encode(rsa.encrypt(f'{keyHash}{self.password}'.encode(), pubKey)))}&username={parse.quote_plus(self.username)}"
        data = f"{param}&sign={self.getSign(param)}"
        headers = {"Content-type": "application/x-www-form-urlencoded"}
        response = self.post(url, data=data, headers=headers)
        while response and response.get("code") == -105:
            self.cookie = f"sid={''.join(random.choices(string.ascii_lowercase + string.digits, k=8))}"
            url = "https://passport.bilibili.com/captcha"
            headers = {
                "Cookie": self.cookie,
                "Host": "passport.bilibili.com",
                "User-Agent": BilibiliLogin.ua,
            }
            response = self.get(url, headers=headers, decode=False)
            if response is None:
                continue
            url = "http://106.75.36.27:19951/captcha/v1"
            img = base64.b64encode(response)
            img = str(img, encoding="utf-8")
            json = {"image": img}
            response = self.post(url, json=json, decode=True)
            print(f"验证码识别结果为: {response['message']}")
            url = "https://passport.bilibili.com/api/v2/oauth2/login"
            param = f"appkey={appKey}&captcha={response['message']}&password={parse.quote_plus(base64.b64encode(rsa.encrypt(f'{keyHash}{self.password}'.encode(), pubKey)))}&username={parse.quote_plus(self.username)}"
            data = f"{param}&sign={self.getSign(param)}"
            headers = {
                "Content-type": "application/x-www-form-urlencoded",
                "Cookie": self.cookie,
            }
            response = self.post(url, data=data, headers=headers)
        if response and response.get("code") == 0:
            self.cookie = ";".join(
                f"{i['name']}={i['value']}"
                for i in response["data"]["cookie_info"]["cookies"]
            )
            self.access_token = response["data"]["token_info"]["access_token"]
            print(f"{self.username}登录成功 {self.cookie} {self.access_token}")
            with open("cookies.txt", "a+", encoding="utf-8") as f:
                f.write(f"{self.username}----{self.cookie}----{self.access_token}\n")
            return self.username, self.cookie, self.access_token
        else:
            print(f"{self.username}登录失败 {response}")

if __name__ == "__main__":
    B = BilibiliLogin()
    B.login()
