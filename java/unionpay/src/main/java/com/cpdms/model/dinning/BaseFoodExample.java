package com.cpdms.model.dinning;

import java.math.BigDecimal;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

/**
 * 菜品设置 BaseFoodExample
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:50:37
 */
public class BaseFoodExample {

    protected String orderByClause;

    protected boolean distinct;

    protected List<Criteria> oredCriteria;

    public BaseFoodExample() {
        oredCriteria = new ArrayList<Criteria>();
    }

    public void setOrderByClause(String orderByClause) {
        this.orderByClause = orderByClause;
    }

    public String getOrderByClause() {
        return orderByClause;
    }

    public void setDistinct(boolean distinct) {
        this.distinct = distinct;
    }

    public boolean isDistinct() {
        return distinct;
    }

    public List<Criteria> getOredCriteria() {
        return oredCriteria;
    }

    public void or(Criteria criteria) {
        oredCriteria.add(criteria);
    }

    public Criteria or() {
        Criteria criteria = createCriteriaInternal();
        oredCriteria.add(criteria);
        return criteria;
    }

    public Criteria createCriteria() {
        Criteria criteria = createCriteriaInternal();
        if (oredCriteria.size() == 0) {
            oredCriteria.add(criteria);
        }
        return criteria;
    }

    protected Criteria createCriteriaInternal() {
        Criteria criteria = new Criteria();
        return criteria;
    }

    public void clear() {
        oredCriteria.clear();
        orderByClause = null;
        distinct = false;
    }

    protected abstract static class GeneratedCriteria {
        protected List<Criterion> criteria;

        protected GeneratedCriteria() {
            super();
            criteria = new ArrayList<Criterion>();
        }

        public boolean isValid() {
            return criteria.size() > 0;
        }

        public List<Criterion> getAllCriteria() {
            return criteria;
        }

        public List<Criterion> getCriteria() {
            return criteria;
        }

        protected void addCriterion(String condition) {
            if (condition == null) {
                throw new RuntimeException("Value for condition cannot be null");
            }
            criteria.add(new Criterion(condition));
        }

        protected void addCriterion(String condition, Object value, String property) {
            if (value == null) {
                throw new RuntimeException("Value for " + property + " cannot be null");
            }
            criteria.add(new Criterion(condition, value));
        }

        protected void addCriterion(String condition, Object value1, Object value2, String property) {
            if (value1 == null || value2 == null) {
                throw new RuntimeException("Between values for " + property + " cannot be null");
            }
            criteria.add(new Criterion(condition, value1, value2));
        }
				
        public Criteria andIdIsNull() {
            addCriterion("id is null");
            return (Criteria) this;
        }

        public Criteria andIdIsNotNull() {
            addCriterion("id is not null");
            return (Criteria) this;
        }

        public Criteria andIdEqualTo(String value) {
            addCriterion("id =", value, "id");
            return (Criteria) this;
        }

        public Criteria andIdNotEqualTo(String value) {
            addCriterion("id <>", value, "id");
            return (Criteria) this;
        }

        public Criteria andIdGreaterThan(String value) {
            addCriterion("id >", value, "id");
            return (Criteria) this;
        }

        public Criteria andIdGreaterThanOrEqualTo(String value) {
            addCriterion("id >=", value, "id");
            return (Criteria) this;
        }

        public Criteria andIdLessThan(String value) {
            addCriterion("id <", value, "id");
            return (Criteria) this;
        }

        public Criteria andIdLessThanOrEqualTo(String value) {
            addCriterion("id <=", value, "id");
            return (Criteria) this;
        }

        public Criteria andIdLike(String value) {
            addCriterion("id like", value, "id");
            return (Criteria) this;
        }

        public Criteria andIdNotLike(String value) {
            addCriterion("id not like", value, "id");
            return (Criteria) this;
        }

        public Criteria andIdIn(List<String> values) {
            addCriterion("id in", values, "id");
            return (Criteria) this;
        }

        public Criteria andIdNotIn(List<String> values) {
            addCriterion("id not in", values, "id");
            return (Criteria) this;
        }

        public Criteria andIdBetween(String value1, String value2) {
            addCriterion("id between", value1, value2, "id");
            return (Criteria) this;
        }

        public Criteria andIdNotBetween(String value1, String value2) {
            addCriterion("id not between", value1, value2, "id");
            return (Criteria) this;
        }
        
			
        public Criteria andFoodCodeIsNull() {
            addCriterion("food_code is null");
            return (Criteria) this;
        }

        public Criteria andFoodCodeIsNotNull() {
            addCriterion("food_code is not null");
            return (Criteria) this;
        }

        public Criteria andFoodCodeEqualTo(String value) {
            addCriterion("food_code =", value, "foodCode");
            return (Criteria) this;
        }

        public Criteria andFoodCodeNotEqualTo(String value) {
            addCriterion("food_code <>", value, "foodCode");
            return (Criteria) this;
        }

        public Criteria andFoodCodeGreaterThan(String value) {
            addCriterion("food_code >", value, "foodCode");
            return (Criteria) this;
        }

        public Criteria andFoodCodeGreaterThanOrEqualTo(String value) {
            addCriterion("food_code >=", value, "foodCode");
            return (Criteria) this;
        }

        public Criteria andFoodCodeLessThan(String value) {
            addCriterion("food_code <", value, "foodCode");
            return (Criteria) this;
        }

        public Criteria andFoodCodeLessThanOrEqualTo(String value) {
            addCriterion("food_code <=", value, "foodCode");
            return (Criteria) this;
        }

        public Criteria andFoodCodeLike(String value) {
            addCriterion("food_code like", value, "foodCode");
            return (Criteria) this;
        }

        public Criteria andFoodCodeNotLike(String value) {
            addCriterion("food_code not like", value, "foodCode");
            return (Criteria) this;
        }

        public Criteria andFoodCodeIn(List<String> values) {
            addCriterion("food_code in", values, "foodCode");
            return (Criteria) this;
        }

        public Criteria andFoodCodeNotIn(List<String> values) {
            addCriterion("food_code not in", values, "foodCode");
            return (Criteria) this;
        }

        public Criteria andFoodCodeBetween(String value1, String value2) {
            addCriterion("food_code between", value1, value2, "foodCode");
            return (Criteria) this;
        }

        public Criteria andFoodCodeNotBetween(String value1, String value2) {
            addCriterion("food_code not between", value1, value2, "foodCode");
            return (Criteria) this;
        }
        
			
        public Criteria andImgIdIsNull() {
            addCriterion("img_id is null");
            return (Criteria) this;
        }

        public Criteria andImgIdIsNotNull() {
            addCriterion("img_id is not null");
            return (Criteria) this;
        }

        public Criteria andImgIdEqualTo(String value) {
            addCriterion("img_id =", value, "imgId");
            return (Criteria) this;
        }

        public Criteria andImgIdNotEqualTo(String value) {
            addCriterion("img_id <>", value, "imgId");
            return (Criteria) this;
        }

        public Criteria andImgIdGreaterThan(String value) {
            addCriterion("img_id >", value, "imgId");
            return (Criteria) this;
        }

        public Criteria andImgIdGreaterThanOrEqualTo(String value) {
            addCriterion("img_id >=", value, "imgId");
            return (Criteria) this;
        }

        public Criteria andImgIdLessThan(String value) {
            addCriterion("img_id <", value, "imgId");
            return (Criteria) this;
        }

        public Criteria andImgIdLessThanOrEqualTo(String value) {
            addCriterion("img_id <=", value, "imgId");
            return (Criteria) this;
        }

        public Criteria andImgIdLike(String value) {
            addCriterion("img_id like", value, "imgId");
            return (Criteria) this;
        }

        public Criteria andImgIdNotLike(String value) {
            addCriterion("img_id not like", value, "imgId");
            return (Criteria) this;
        }

        public Criteria andImgIdIn(List<String> values) {
            addCriterion("img_id in", values, "imgId");
            return (Criteria) this;
        }

        public Criteria andImgIdNotIn(List<String> values) {
            addCriterion("img_id not in", values, "imgId");
            return (Criteria) this;
        }

        public Criteria andImgIdBetween(String value1, String value2) {
            addCriterion("img_id between", value1, value2, "imgId");
            return (Criteria) this;
        }

        public Criteria andImgIdNotBetween(String value1, String value2) {
            addCriterion("img_id not between", value1, value2, "imgId");
            return (Criteria) this;
        }
        
			
        public Criteria andFoodNameIsNull() {
            addCriterion("food_name is null");
            return (Criteria) this;
        }

        public Criteria andFoodNameIsNotNull() {
            addCriterion("food_name is not null");
            return (Criteria) this;
        }

        public Criteria andFoodNameEqualTo(String value) {
            addCriterion("food_name =", value, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameNotEqualTo(String value) {
            addCriterion("food_name <>", value, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameGreaterThan(String value) {
            addCriterion("food_name >", value, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameGreaterThanOrEqualTo(String value) {
            addCriterion("food_name >=", value, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameLessThan(String value) {
            addCriterion("food_name <", value, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameLessThanOrEqualTo(String value) {
            addCriterion("food_name <=", value, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameLike(String value) {
            addCriterion("food_name like", value, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameNotLike(String value) {
            addCriterion("food_name not like", value, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameIn(List<String> values) {
            addCriterion("food_name in", values, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameNotIn(List<String> values) {
            addCriterion("food_name not in", values, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameBetween(String value1, String value2) {
            addCriterion("food_name between", value1, value2, "foodName");
            return (Criteria) this;
        }

        public Criteria andFoodNameNotBetween(String value1, String value2) {
            addCriterion("food_name not between", value1, value2, "foodName");
            return (Criteria) this;
        }
        
			
        public Criteria andFoodUnitIsNull() {
            addCriterion("food_unit is null");
            return (Criteria) this;
        }

        public Criteria andFoodUnitIsNotNull() {
            addCriterion("food_unit is not null");
            return (Criteria) this;
        }

        public Criteria andFoodUnitEqualTo(String value) {
            addCriterion("food_unit =", value, "foodUnit");
            return (Criteria) this;
        }

        public Criteria andFoodUnitNotEqualTo(String value) {
            addCriterion("food_unit <>", value, "foodUnit");
            return (Criteria) this;
        }

        public Criteria andFoodUnitGreaterThan(String value) {
            addCriterion("food_unit >", value, "foodUnit");
            return (Criteria) this;
        }

        public Criteria andFoodUnitGreaterThanOrEqualTo(String value) {
            addCriterion("food_unit >=", value, "foodUnit");
            return (Criteria) this;
        }

        public Criteria andFoodUnitLessThan(String value) {
            addCriterion("food_unit <", value, "foodUnit");
            return (Criteria) this;
        }

        public Criteria andFoodUnitLessThanOrEqualTo(String value) {
            addCriterion("food_unit <=", value, "foodUnit");
            return (Criteria) this;
        }

        public Criteria andFoodUnitLike(String value) {
            addCriterion("food_unit like", value, "foodUnit");
            return (Criteria) this;
        }

        public Criteria andFoodUnitNotLike(String value) {
            addCriterion("food_unit not like", value, "foodUnit");
            return (Criteria) this;
        }

        public Criteria andFoodUnitIn(List<String> values) {
            addCriterion("food_unit in", values, "foodUnit");
            return (Criteria) this;
        }

        public Criteria andFoodUnitNotIn(List<String> values) {
            addCriterion("food_unit not in", values, "foodUnit");
            return (Criteria) this;
        }

        public Criteria andFoodUnitBetween(String value1, String value2) {
            addCriterion("food_unit between", value1, value2, "foodUnit");
            return (Criteria) this;
        }

        public Criteria andFoodUnitNotBetween(String value1, String value2) {
            addCriterion("food_unit not between", value1, value2, "foodUnit");
            return (Criteria) this;
        }
        
			
        public Criteria andFoodPriceIsNull() {
            addCriterion("food_price is null");
            return (Criteria) this;
        }

        public Criteria andFoodPriceIsNotNull() {
            addCriterion("food_price is not null");
            return (Criteria) this;
        }

        public Criteria andFoodPriceEqualTo(BigDecimal value) {
            addCriterion("food_price =", value, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceNotEqualTo(BigDecimal value) {
            addCriterion("food_price <>", value, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceGreaterThan(BigDecimal value) {
            addCriterion("food_price >", value, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceGreaterThanOrEqualTo(BigDecimal value) {
            addCriterion("food_price >=", value, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceLessThan(BigDecimal value) {
            addCriterion("food_price <", value, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceLessThanOrEqualTo(BigDecimal value) {
            addCriterion("food_price <=", value, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceLike(BigDecimal value) {
            addCriterion("food_price like", value, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceNotLike(BigDecimal value) {
            addCriterion("food_price not like", value, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceIn(List<BigDecimal> values) {
            addCriterion("food_price in", values, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceNotIn(List<BigDecimal> values) {
            addCriterion("food_price not in", values, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceBetween(BigDecimal value1, BigDecimal value2) {
            addCriterion("food_price between", value1, value2, "foodPrice");
            return (Criteria) this;
        }

        public Criteria andFoodPriceNotBetween(BigDecimal value1, BigDecimal value2) {
            addCriterion("food_price not between", value1, value2, "foodPrice");
            return (Criteria) this;
        }
        
			
        public Criteria andFoodPackIsNull() {
            addCriterion("food_pack is null");
            return (Criteria) this;
        }

        public Criteria andFoodPackIsNotNull() {
            addCriterion("food_pack is not null");
            return (Criteria) this;
        }

        public Criteria andFoodPackEqualTo(String value) {
            addCriterion("food_pack =", value, "foodPack");
            return (Criteria) this;
        }

        public Criteria andFoodPackNotEqualTo(String value) {
            addCriterion("food_pack <>", value, "foodPack");
            return (Criteria) this;
        }

        public Criteria andFoodPackGreaterThan(String value) {
            addCriterion("food_pack >", value, "foodPack");
            return (Criteria) this;
        }

        public Criteria andFoodPackGreaterThanOrEqualTo(String value) {
            addCriterion("food_pack >=", value, "foodPack");
            return (Criteria) this;
        }

        public Criteria andFoodPackLessThan(String value) {
            addCriterion("food_pack <", value, "foodPack");
            return (Criteria) this;
        }

        public Criteria andFoodPackLessThanOrEqualTo(String value) {
            addCriterion("food_pack <=", value, "foodPack");
            return (Criteria) this;
        }

        public Criteria andFoodPackLike(String value) {
            addCriterion("food_pack like", value, "foodPack");
            return (Criteria) this;
        }

        public Criteria andFoodPackNotLike(String value) {
            addCriterion("food_pack not like", value, "foodPack");
            return (Criteria) this;
        }

        public Criteria andFoodPackIn(List<String> values) {
            addCriterion("food_pack in", values, "foodPack");
            return (Criteria) this;
        }

        public Criteria andFoodPackNotIn(List<String> values) {
            addCriterion("food_pack not in", values, "foodPack");
            return (Criteria) this;
        }

        public Criteria andFoodPackBetween(String value1, String value2) {
            addCriterion("food_pack between", value1, value2, "foodPack");
            return (Criteria) this;
        }

        public Criteria andFoodPackNotBetween(String value1, String value2) {
            addCriterion("food_pack not between", value1, value2, "foodPack");
            return (Criteria) this;
        }
        
			
        public Criteria andFoodSpecIsNull() {
            addCriterion("food_spec is null");
            return (Criteria) this;
        }

        public Criteria andFoodSpecIsNotNull() {
            addCriterion("food_spec is not null");
            return (Criteria) this;
        }

        public Criteria andFoodSpecEqualTo(String value) {
            addCriterion("food_spec =", value, "foodSpec");
            return (Criteria) this;
        }

        public Criteria andFoodSpecNotEqualTo(String value) {
            addCriterion("food_spec <>", value, "foodSpec");
            return (Criteria) this;
        }

        public Criteria andFoodSpecGreaterThan(String value) {
            addCriterion("food_spec >", value, "foodSpec");
            return (Criteria) this;
        }

        public Criteria andFoodSpecGreaterThanOrEqualTo(String value) {
            addCriterion("food_spec >=", value, "foodSpec");
            return (Criteria) this;
        }

        public Criteria andFoodSpecLessThan(String value) {
            addCriterion("food_spec <", value, "foodSpec");
            return (Criteria) this;
        }

        public Criteria andFoodSpecLessThanOrEqualTo(String value) {
            addCriterion("food_spec <=", value, "foodSpec");
            return (Criteria) this;
        }

        public Criteria andFoodSpecLike(String value) {
            addCriterion("food_spec like", value, "foodSpec");
            return (Criteria) this;
        }

        public Criteria andFoodSpecNotLike(String value) {
            addCriterion("food_spec not like", value, "foodSpec");
            return (Criteria) this;
        }

        public Criteria andFoodSpecIn(List<String> values) {
            addCriterion("food_spec in", values, "foodSpec");
            return (Criteria) this;
        }

        public Criteria andFoodSpecNotIn(List<String> values) {
            addCriterion("food_spec not in", values, "foodSpec");
            return (Criteria) this;
        }

        public Criteria andFoodSpecBetween(String value1, String value2) {
            addCriterion("food_spec between", value1, value2, "foodSpec");
            return (Criteria) this;
        }

        public Criteria andFoodSpecNotBetween(String value1, String value2) {
            addCriterion("food_spec not between", value1, value2, "foodSpec");
            return (Criteria) this;
        }
        
			
        public Criteria andFoodStatusIsNull() {
            addCriterion("food_status is null");
            return (Criteria) this;
        }

        public Criteria andFoodStatusIsNotNull() {
            addCriterion("food_status is not null");
            return (Criteria) this;
        }

        public Criteria andFoodStatusEqualTo(Integer value) {
            addCriterion("food_status =", value, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusNotEqualTo(Integer value) {
            addCriterion("food_status <>", value, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusGreaterThan(Integer value) {
            addCriterion("food_status >", value, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusGreaterThanOrEqualTo(Integer value) {
            addCriterion("food_status >=", value, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusLessThan(Integer value) {
            addCriterion("food_status <", value, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusLessThanOrEqualTo(Integer value) {
            addCriterion("food_status <=", value, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusLike(Integer value) {
            addCriterion("food_status like", value, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusNotLike(Integer value) {
            addCriterion("food_status not like", value, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusIn(List<Integer> values) {
            addCriterion("food_status in", values, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusNotIn(List<Integer> values) {
            addCriterion("food_status not in", values, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusBetween(Integer value1, Integer value2) {
            addCriterion("food_status between", value1, value2, "foodStatus");
            return (Criteria) this;
        }

        public Criteria andFoodStatusNotBetween(Integer value1, Integer value2) {
            addCriterion("food_status not between", value1, value2, "foodStatus");
            return (Criteria) this;
        }
        
			
        public Criteria andCreateByIsNull() {
            addCriterion("create_by is null");
            return (Criteria) this;
        }

        public Criteria andCreateByIsNotNull() {
            addCriterion("create_by is not null");
            return (Criteria) this;
        }

        public Criteria andCreateByEqualTo(String value) {
            addCriterion("create_by =", value, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByNotEqualTo(String value) {
            addCriterion("create_by <>", value, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByGreaterThan(String value) {
            addCriterion("create_by >", value, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByGreaterThanOrEqualTo(String value) {
            addCriterion("create_by >=", value, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByLessThan(String value) {
            addCriterion("create_by <", value, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByLessThanOrEqualTo(String value) {
            addCriterion("create_by <=", value, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByLike(String value) {
            addCriterion("create_by like", value, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByNotLike(String value) {
            addCriterion("create_by not like", value, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByIn(List<String> values) {
            addCriterion("create_by in", values, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByNotIn(List<String> values) {
            addCriterion("create_by not in", values, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByBetween(String value1, String value2) {
            addCriterion("create_by between", value1, value2, "createBy");
            return (Criteria) this;
        }

        public Criteria andCreateByNotBetween(String value1, String value2) {
            addCriterion("create_by not between", value1, value2, "createBy");
            return (Criteria) this;
        }
        
			
        public Criteria andCreateDateIsNull() {
            addCriterion("create_date is null");
            return (Criteria) this;
        }

        public Criteria andCreateDateIsNotNull() {
            addCriterion("create_date is not null");
            return (Criteria) this;
        }

        public Criteria andCreateDateEqualTo(Date value) {
            addCriterion("create_date =", value, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateNotEqualTo(Date value) {
            addCriterion("create_date <>", value, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateGreaterThan(Date value) {
            addCriterion("create_date >", value, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateGreaterThanOrEqualTo(Date value) {
            addCriterion("create_date >=", value, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateLessThan(Date value) {
            addCriterion("create_date <", value, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateLessThanOrEqualTo(Date value) {
            addCriterion("create_date <=", value, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateLike(Date value) {
            addCriterion("create_date like", value, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateNotLike(Date value) {
            addCriterion("create_date not like", value, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateIn(List<Date> values) {
            addCriterion("create_date in", values, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateNotIn(List<Date> values) {
            addCriterion("create_date not in", values, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateBetween(Date value1, Date value2) {
            addCriterion("create_date between", value1, value2, "createDate");
            return (Criteria) this;
        }

        public Criteria andCreateDateNotBetween(Date value1, Date value2) {
            addCriterion("create_date not between", value1, value2, "createDate");
            return (Criteria) this;
        }
        
			
        public Criteria andUpdateByIsNull() {
            addCriterion("update_by is null");
            return (Criteria) this;
        }

        public Criteria andUpdateByIsNotNull() {
            addCriterion("update_by is not null");
            return (Criteria) this;
        }

        public Criteria andUpdateByEqualTo(String value) {
            addCriterion("update_by =", value, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByNotEqualTo(String value) {
            addCriterion("update_by <>", value, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByGreaterThan(String value) {
            addCriterion("update_by >", value, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByGreaterThanOrEqualTo(String value) {
            addCriterion("update_by >=", value, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByLessThan(String value) {
            addCriterion("update_by <", value, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByLessThanOrEqualTo(String value) {
            addCriterion("update_by <=", value, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByLike(String value) {
            addCriterion("update_by like", value, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByNotLike(String value) {
            addCriterion("update_by not like", value, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByIn(List<String> values) {
            addCriterion("update_by in", values, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByNotIn(List<String> values) {
            addCriterion("update_by not in", values, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByBetween(String value1, String value2) {
            addCriterion("update_by between", value1, value2, "updateBy");
            return (Criteria) this;
        }

        public Criteria andUpdateByNotBetween(String value1, String value2) {
            addCriterion("update_by not between", value1, value2, "updateBy");
            return (Criteria) this;
        }
        
			
        public Criteria andUpdateDateIsNull() {
            addCriterion("update_date is null");
            return (Criteria) this;
        }

        public Criteria andUpdateDateIsNotNull() {
            addCriterion("update_date is not null");
            return (Criteria) this;
        }

        public Criteria andUpdateDateEqualTo(Date value) {
            addCriterion("update_date =", value, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateNotEqualTo(Date value) {
            addCriterion("update_date <>", value, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateGreaterThan(Date value) {
            addCriterion("update_date >", value, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateGreaterThanOrEqualTo(Date value) {
            addCriterion("update_date >=", value, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateLessThan(Date value) {
            addCriterion("update_date <", value, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateLessThanOrEqualTo(Date value) {
            addCriterion("update_date <=", value, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateLike(Date value) {
            addCriterion("update_date like", value, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateNotLike(Date value) {
            addCriterion("update_date not like", value, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateIn(List<Date> values) {
            addCriterion("update_date in", values, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateNotIn(List<Date> values) {
            addCriterion("update_date not in", values, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateBetween(Date value1, Date value2) {
            addCriterion("update_date between", value1, value2, "updateDate");
            return (Criteria) this;
        }

        public Criteria andUpdateDateNotBetween(Date value1, Date value2) {
            addCriterion("update_date not between", value1, value2, "updateDate");
            return (Criteria) this;
        }
        
			
        public Criteria andStatusIsNull() {
            addCriterion("status is null");
            return (Criteria) this;
        }

        public Criteria andStatusIsNotNull() {
            addCriterion("status is not null");
            return (Criteria) this;
        }

        public Criteria andStatusEqualTo(Integer value) {
            addCriterion("status =", value, "status");
            return (Criteria) this;
        }

        public Criteria andStatusNotEqualTo(Integer value) {
            addCriterion("status <>", value, "status");
            return (Criteria) this;
        }

        public Criteria andStatusGreaterThan(Integer value) {
            addCriterion("status >", value, "status");
            return (Criteria) this;
        }

        public Criteria andStatusGreaterThanOrEqualTo(Integer value) {
            addCriterion("status >=", value, "status");
            return (Criteria) this;
        }

        public Criteria andStatusLessThan(Integer value) {
            addCriterion("status <", value, "status");
            return (Criteria) this;
        }

        public Criteria andStatusLessThanOrEqualTo(Integer value) {
            addCriterion("status <=", value, "status");
            return (Criteria) this;
        }

        public Criteria andStatusLike(Integer value) {
            addCriterion("status like", value, "status");
            return (Criteria) this;
        }

        public Criteria andStatusNotLike(Integer value) {
            addCriterion("status not like", value, "status");
            return (Criteria) this;
        }

        public Criteria andStatusIn(List<Integer> values) {
            addCriterion("status in", values, "status");
            return (Criteria) this;
        }

        public Criteria andStatusNotIn(List<Integer> values) {
            addCriterion("status not in", values, "status");
            return (Criteria) this;
        }

        public Criteria andStatusBetween(Integer value1, Integer value2) {
            addCriterion("status between", value1, value2, "status");
            return (Criteria) this;
        }

        public Criteria andStatusNotBetween(Integer value1, Integer value2) {
            addCriterion("status not between", value1, value2, "status");
            return (Criteria) this;
        }
        
	}

    public static class Criteria extends GeneratedCriteria {

        protected Criteria() {
            super();
        }
    }

    public static class Criterion {
        private String condition;

        private Object value;

        private Object secondValue;

        private boolean noValue;

        private boolean singleValue;

        private boolean betweenValue;

        private boolean listValue;

        private String typeHandler;

        public String getCondition() {
            return condition;
        }

        public Object getValue() {
            return value;
        }

        public Object getSecondValue() {
            return secondValue;
        }

        public boolean isNoValue() {
            return noValue;
        }

        public boolean isSingleValue() {
            return singleValue;
        }

        public boolean isBetweenValue() {
            return betweenValue;
        }

        public boolean isListValue() {
            return listValue;
        }

        public String getTypeHandler() {
            return typeHandler;
        }

        protected Criterion(String condition) {
            super();
            this.condition = condition;
            this.typeHandler = null;
            this.noValue = true;
        }

        protected Criterion(String condition, Object value, String typeHandler) {
            super();
            this.condition = condition;
            this.value = value;
            this.typeHandler = typeHandler;
            if (value instanceof List<?>) {
                this.listValue = true;
            } else {
                this.singleValue = true;
            }
        }

        protected Criterion(String condition, Object value) {
            this(condition, value, null);
        }

        protected Criterion(String condition, Object value, Object secondValue, String typeHandler) {
            super();
            this.condition = condition;
            this.value = value;
            this.secondValue = secondValue;
            this.typeHandler = typeHandler;
            this.betweenValue = true;
        }

        protected Criterion(String condition, Object value, Object secondValue) {
            this(condition, value, secondValue, null);
        }
    }
}