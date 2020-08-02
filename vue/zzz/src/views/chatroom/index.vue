<template>
  <div class="chatroom-container">
    <div class="chatroom-header">在线聊天室</div>
    <div class="chatroom-body">
      <div class="chatroom-messages">
        <!-- 系统消息 -->
        <div class="message-item" v-for="(msg, index) in systemMessages" :key="'system'+index">
          <div class="message-system">
            <div class="tip">{{ msg }}</div>
          </div>
        </div>
        <div class="message-item" v-for="(msg, index) in userMessages" :key="'user'+index">
          <div :class="msg.from === username ? 'right' : ''">
            <div class="message-from">{{ msg.from }}</div>
            <div class="message-message">{{ msg.message }}</div>
          </div>
        </div>
      </div>
      <div class="chatroom-sendbox">
        <el-input type="textarea" :rows="2" resize="none" v-model="message" placeholder="请输入内容"></el-input>
        <el-button type="success" @click="send">发送消息</el-button>
      </div>
    </div>

    <el-collapse accordion class="chatroom-members">
      <el-collapse-item>
        <template slot="title">在线用户</template>
        <div v-for="(user, index) in userList" :key="index">{{ user }}</div>
      </el-collapse-item>
    </el-collapse>
  </div>
</template>

<script>
import { getToken } from "@/utils/auth";

export default {
  name: "Chatroom",
  data() {
    return {
      userMessages: [],
      systemMessages: [],
      userList: [],
      message: "",
      ws: null
    };
  },
  computed: {
    username() {
      return this.$store.getters.name;
    }
  },
  created() {
    this.initSocket();
  },
  destroyed() {
    this.websocketClose();
  },
  methods: {
    initSocket() {
      // 初始化websocket
      const token = getToken().split(" ")[1];
      const wsuri =
        "ws://" +
        location.host +
        process.env.VUE_APP_WEBSOCKET_API +
        "/chatroom?token=" +
        token;
      this.ws = new WebSocket(wsuri);
      this.ws.onerror = this.sendErrorMessage;
      this.ws.onopen = this.sendOpenMessage;
      this.ws.onmessage = this.sendOnmessageMessage;
      this.ws.onclose = this.sendCloseMessage;
      // 页面关闭的时候关闭websocket
      window.onbeforeunload = this.websocketClose;
    },
    send() {
      this.ws.send(this.message);
      this.message = "";
    },
    sendErrorMessage() {
      this.$message({
        message: "发送失败，请重试",
        type: "error"
      });
      this.$router.push('/')
    },
    sendOpenMessage() {
      this.$message({
        message: "已进入聊天室",
        type: "success"
      });
    },
    sendOnmessageMessage(e) {
      const msg = JSON.parse(e.data);
      switch (msg.type) {
        case 0:
          this.systemMessages.push(msg.message);
          break;
        case 1:
          this.userMessages.push({ from: msg.from, message: msg.message });
          break;
        case 2:
          this.userList = msg.message;
          break;
        default:
          break;
      }
    },
    sendCloseMessage() {
      this.$message({
        message: "连接已关闭",
        type: "error"
      });
      this.$router.push('/')
    },
    websocketClose() {
      this.ws.close();
    }
  }
};
</script>

<style lang="scss" scoped>
.chatroom-container {
  background: #fff;
  padding: 20px;
  .chatroom-header {
    line-height: 30px;
    text-align: center;
    font-size: 16px;
  }
  .chatroom-body {
    .chatroom-messages {
      border: 1px solid #f0f2f5;
      height: 600px;
      overflow-y: auto;
      .message-item {
        margin: 10px;
        font-size: 14px;
        .message-system {
          font-size: 12px;
          text-align: center;
          color: grey;
          .tip {
            padding: 7px;
            display: inline-block;
            margin: 0 auto;
            background-color: #e5e5e5;
            border-radius: 7px;
          }
        }
        .message-from {
          color: grey;
          margin-bottom: 5px;
        }
        .message-message {
          color: #333;
          padding: 7px;
          line-height: 20px;
          border: 1px solid #456;
          border-radius: 7px;
          display: inline-block;
          background: #a6d4f2;
        }
        .right {
          text-align: right;
        }
      }
    }
    .chatroom-messages::-webkit-scrollbar {
      width: 4px;
      height: 4px;
    }
    .chatroom-messages::-webkit-scrollbar-thumb {
      border-radius: 5px;
      box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
      background: rgba(0, 0, 0, 0.2);
    }
    .chatroom-messages::-webkit-scrollbar-track {
      box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
      border-radius: 0;
      background: rgba(0, 0, 0, 0.1);
    }
    .chatroom-sendbox {
      display: flex;
      margin-top: 10px;
      .el-textarea {
        flex: 1;
      }
      .el-button {
        margin-left: 10px;
        width: 100px;
        flex: none;
      }
    }
  }
  /deep/.chatroom-members {
    position: fixed;
    border: 1px solid #e5e5e5;
    bottom: 40%;
    right: 3px;
    .el-collapse-item__header {
      padding-left: 5px;
      background-color: #d6d8db;
    }
    .el-collapse-item__content {
      background-color: #f0f2f5;
    }
  }
}
</style>
