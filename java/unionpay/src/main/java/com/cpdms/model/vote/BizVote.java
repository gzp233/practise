package com.cpdms.model.vote;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import java.io.Serializable;

/**
 *  BizVote
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-27 10:42:13
 */
public class BizVote implements Serializable {

	private static final long serialVersionUID = 1L;


	/** ID **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

	/** 卡号 **/
    @JsonSerialize(using= StringFormat.class)
	private String cardNo;

	/** 投票项目ID **/
    @JsonSerialize(using= StringFormat.class)
	private String itemId;


	public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }


	public String getCardNo() {
        return cardNo;
    }

    public void setCardNo(String cardNo) {
        this.cardNo = cardNo;
    }


	public String getItemId() {
        return itemId;
    }

    public void setItemId(String itemId) {
        this.itemId = itemId;
    }


	public BizVote() {
        super();
    }


	public BizVote(String id,String cardNo,String itemId) {

		this.id = id;
		this.cardNo = cardNo;
		this.itemId = itemId;

	}

}
