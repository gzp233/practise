package com.cpdms.model.dinning;

import com.cpdms.model.StringFormat;
import com.fasterxml.jackson.databind.annotation.JsonSerialize;

import javax.validation.constraints.Min;
import javax.validation.constraints.NotBlank;
import javax.validation.constraints.NotNull;
import java.io.Serializable;
import java.math.BigDecimal;
import java.lang.Integer;

/**
 * 预定订单菜品 BizOrdFood
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:45:21
 */
public class BizOrdFood implements Serializable {

	private static final long serialVersionUID = 1L;


	/** 主键id **/
	@JsonSerialize(using= StringFormat.class)
	private String id;

	/** 订单id **/
	@NotBlank(message = "订餐ID不能为空")
	@JsonSerialize(using= StringFormat.class)
	private String ordId;

	/** 菜品id **/
	@NotBlank(message = "订餐ID不能为空")
	@JsonSerialize(using= StringFormat.class)
	private String foodId;
	
	@JsonSerialize(using= StringFormat.class)
	private String bofDeptId;

	/** 单价 **/
	private BigDecimal price;

	/** 数量 **/
	@NotNull(message = "数量不能为null")
    @Min(value = 1, message = "数量至少为1")
	private BigDecimal qty;

	/** 顺序 **/
	private Integer seq;

	private BaseFood baseFood;
	@JsonSerialize(using= StringFormat.class)
	private String foodName;
	@JsonSerialize(using= StringFormat.class)
	private String imgPath;
	@JsonSerialize(using= StringFormat.class)
	private String rangeName;
	
	private Integer amount;

    public String getFoodName() {
		return foodName;
	}

	public void setFoodName(String foodName) {
		this.foodName = foodName;
	}

	public String getImgPath() {
		return imgPath;
	}

	public void setImgPath(String imgPath) {
		this.imgPath = imgPath;
	}

	public String getRangeName() {
		return rangeName;
	}

	public void setRangeName(String rangeName) {
		this.rangeName = rangeName;
	}

	public Integer getAmount() {
		return amount;
	}

	public void setAmount(Integer amount) {
		this.amount = amount;
	}

	public BaseFood getBaseFood() {
        return baseFood;
    }

    public void setBaseFood(BaseFood baseFood) {
        this.baseFood = baseFood;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }


	public String getOrdId() {
        return ordId;
    }

    public void setOrdId(String ordId) {
        this.ordId = ordId;
    }


	public String getFoodId() {
        return foodId;
    }

    public void setFoodId(String foodId) {
        this.foodId = foodId;
    }


	public String getBofDeptId() {
		return bofDeptId;
	}

	public void setBofDeptId(String bofDeptId) {
		this.bofDeptId = bofDeptId;
	}

	public BigDecimal getPrice() {
        return price;
    }

    public void setPrice(BigDecimal price) {
        this.price = price;
    }


	public BigDecimal getQty() {
        return qty;
    }

    public void setQty(BigDecimal qty) {
        this.qty = qty;
    }


	public Integer getSeq() {
        return seq;
    }

    public void setSeq(Integer seq) {
        this.seq = seq;
    }


	public BizOrdFood() {
        super();
    }


	public BizOrdFood(String id,String ordId,String foodId,BigDecimal price,BigDecimal qty,Integer seq) {

		this.id = id;
		this.ordId = ordId;
		this.foodId = foodId;
		this.price = price;
		this.qty = qty;
		this.seq = seq;

	}

}
