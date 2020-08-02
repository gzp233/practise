package com.cpdms.model.vote;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import java.io.Serializable;
import java.lang.Integer;

/**
 *  BaseVoteItem
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-27 10:42:29
 */
public class BaseVoteItem implements Serializable {

	private static final long serialVersionUID = 1L;


	/** ID **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

	/** 选项名称 **/
    @JsonSerialize(using= StringFormat.class)
	private String itemName;

	/** 投票ID **/
    @JsonSerialize(using= StringFormat.class)
	private String voteId;

	/** 投票数量 **/
	private Integer num;

    @JsonSerialize(using= StringFormat.class)
	private String percent;

    public String getPercent() {
        return percent;
    }

    public void setPercent(String percent) {
        this.percent = percent;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }


	public String getItemName() {
        return itemName;
    }

    public void setItemName(String itemName) {
        this.itemName = itemName;
    }


	public String getVoteId() {
        return voteId;
    }

    public void setVoteId(String voteId) {
        this.voteId = voteId;
    }


	public Integer getNum() {
        return num;
    }

    public void setNum(Integer num) {
        this.num = num;
    }


	public BaseVoteItem() {
        super();
    }


	public BaseVoteItem(String id,String itemName,String voteId,Integer num) {

		this.id = id;
		this.itemName = itemName;
		this.voteId = voteId;
		this.num = num;

	}

}
