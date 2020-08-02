package com.cpdms.model.hair;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import javax.validation.constraints.Min;
import javax.validation.constraints.NotBlank;
import javax.validation.constraints.NotNull;
import java.io.Serializable;
import java.math.BigDecimal;

/**
 * 美发订单项目表 BizHairOrdHair
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-14 10:56:38
 */
public class BizHairOrdHair implements Serializable {

	private static final long serialVersionUID = 1L;


	/** ID **/
    @JsonSerialize(using= StringFormat.class)
	private String id;

	/** 项目ID **/
	@NotBlank(message = "项目ID不能为空")
    @JsonSerialize(using= StringFormat.class)
	private String hairId;

	/** 美发订单ID **/
    @JsonSerialize(using= StringFormat.class)
	private String hairOrdId;

	/** 价格 **/
	@NotNull(message = "价格不能为空")
    @Min(value = 0, message = "价格不能低于0")
	private BigDecimal price;

	private BaseHair baseHair;

    public BaseHair getBaseHair() {
        return baseHair;
    }

    public void setBaseHair(BaseHair baseHair) {
        this.baseHair = baseHair;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }


	public String getHairId() {
        return hairId;
    }

    public void setHairId(String hairId) {
        this.hairId = hairId;
    }


	public String getHairOrdId() {
        return hairOrdId;
    }

    public void setHairOrdId(String hairOrdId) {
        this.hairOrdId = hairOrdId;
    }


	public BigDecimal getPrice() {
        return price;
    }

    public void setPrice(BigDecimal price) {
        this.price = price;
    }


	public BizHairOrdHair() {
        super();
    }


	public BizHairOrdHair(String id,String hairId,String hairOrdId,BigDecimal price) {

		this.id = id;
		this.hairId = hairId;
		this.hairOrdId = hairOrdId;
		this.price = price;

	}

}
