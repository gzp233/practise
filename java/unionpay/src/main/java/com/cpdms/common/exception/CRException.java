package com.cpdms.common.exception;

public class CRException extends RuntimeException {
    private static final long serialVersionUID = 1L;

    private String msg;
    private int code = 500;

    public CRException(String msg) {
        super(msg);
        this.msg = msg;
    }

    public CRException(String msg, Throwable e) {
        super(msg, e);
        this.msg = msg;
    }

    public CRException(String msg, int code) {
        super(msg);
        this.msg = msg;
        this.code = code;
    }

    public CRException(String msg, int code, Throwable e) {
        super(msg, e);
        this.msg = msg;
        this.code = code;
    }

    public String getMsg() {
        return msg;
    }

    public void setMsg(String msg) {
        this.msg = msg;
    }

    public int getCode() {
        return code;
    }

    public void setCode(int code) {
        this.code = code;
    }

}
