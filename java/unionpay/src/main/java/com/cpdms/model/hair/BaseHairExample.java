package com.cpdms.model.hair;

import java.math.BigDecimal;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

/**
 * 美发项目表 BaseHairExample
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-14 10:57:06
 */
public class BaseHairExample {

    protected String orderByClause;

    protected boolean distinct;

    protected List<Criteria> oredCriteria;

    public BaseHairExample() {
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


        public Criteria andHairNameIsNull() {
            addCriterion("hair_name is null");
            return (Criteria) this;
        }

        public Criteria andHairNameIsNotNull() {
            addCriterion("hair_name is not null");
            return (Criteria) this;
        }

        public Criteria andHairNameEqualTo(String value) {
            addCriterion("hair_name =", value, "hairName");
            return (Criteria) this;
        }

        public Criteria andHairNameNotEqualTo(String value) {
            addCriterion("hair_name <>", value, "hairName");
            return (Criteria) this;
        }

        public Criteria andHairNameGreaterThan(String value) {
            addCriterion("hair_name >", value, "hairName");
            return (Criteria) this;
        }

        public Criteria andHairNameGreaterThanOrEqualTo(String value) {
            addCriterion("hair_name >=", value, "hairName");
            return (Criteria) this;
        }

        public Criteria andHairNameLessThan(String value) {
            addCriterion("hair_name <", value, "hairName");
            return (Criteria) this;
        }

        public Criteria andHairNameLessThanOrEqualTo(String value) {
            addCriterion("hair_name <=", value, "hairName");
            return (Criteria) this;
        }

        public Criteria andHairNameLike(String value) {
            addCriterion("hair_name like", value, "hairName");
            return (Criteria) this;
        }

        public Criteria andHairNameNotLike(String value) {
            addCriterion("hair_name not like", value, "hairName");
            return (Criteria) this;
        }

        public Criteria andHairNameIn(List<String> values) {
            addCriterion("hair_name in", values, "hairName");
            return (Criteria) this;
        }

        public Criteria andHairNameNotIn(List<String> values) {
            addCriterion("hair_name not in", values, "hairName");
            return (Criteria) this;
        }

        public Criteria andHairNameBetween(String value1, String value2) {
            addCriterion("hair_name between", value1, value2, "hairName");
            return (Criteria) this;
        }

        public Criteria andHairNameNotBetween(String value1, String value2) {
            addCriterion("hair_name not between", value1, value2, "hairName");
            return (Criteria) this;
        }


        public Criteria andHairTypeIsNull() {
            addCriterion("hair_type is null");
            return (Criteria) this;
        }

        public Criteria andHairTypeIsNotNull() {
            addCriterion("hair_type is not null");
            return (Criteria) this;
        }

        public Criteria andHairTypeEqualTo(Integer value) {
            addCriterion("hair_type =", value, "hairType");
            return (Criteria) this;
        }

        public Criteria andHairTypeNotEqualTo(Integer value) {
            addCriterion("hair_type <>", value, "hairType");
            return (Criteria) this;
        }

        public Criteria andHairTypeGreaterThan(Integer value) {
            addCriterion("hair_type >", value, "hairType");
            return (Criteria) this;
        }

        public Criteria andHairTypeGreaterThanOrEqualTo(Integer value) {
            addCriterion("hair_type >=", value, "hairType");
            return (Criteria) this;
        }

        public Criteria andHairTypeLessThan(Integer value) {
            addCriterion("hair_type <", value, "hairType");
            return (Criteria) this;
        }

        public Criteria andHairTypeLessThanOrEqualTo(Integer value) {
            addCriterion("hair_type <=", value, "hairType");
            return (Criteria) this;
        }

        public Criteria andHairTypeLike(Integer value) {
            addCriterion("hair_type like", value, "hairType");
            return (Criteria) this;
        }

        public Criteria andHairTypeNotLike(Integer value) {
            addCriterion("hair_type not like", value, "hairType");
            return (Criteria) this;
        }

        public Criteria andHairTypeIn(List<Integer> values) {
            addCriterion("hair_type in", values, "hairType");
            return (Criteria) this;
        }

        public Criteria andHairTypeNotIn(List<Integer> values) {
            addCriterion("hair_type not in", values, "hairType");
            return (Criteria) this;
        }

        public Criteria andHairTypeBetween(Integer value1, Integer value2) {
            addCriterion("hair_type between", value1, value2, "hairType");
            return (Criteria) this;
        }

        public Criteria andHairTypeNotBetween(Integer value1, Integer value2) {
            addCriterion("hair_type not between", value1, value2, "hairType");
            return (Criteria) this;
        }


        public Criteria andHairPriceIsNull() {
            addCriterion("hair_price is null");
            return (Criteria) this;
        }

        public Criteria andHairPriceIsNotNull() {
            addCriterion("hair_price is not null");
            return (Criteria) this;
        }

        public Criteria andHairPriceEqualTo(BigDecimal value) {
            addCriterion("hair_price =", value, "hairPrice");
            return (Criteria) this;
        }

        public Criteria andHairPriceNotEqualTo(BigDecimal value) {
            addCriterion("hair_price <>", value, "hairPrice");
            return (Criteria) this;
        }

        public Criteria andHairPriceGreaterThan(BigDecimal value) {
            addCriterion("hair_price >", value, "hairPrice");
            return (Criteria) this;
        }

        public Criteria andHairPriceGreaterThanOrEqualTo(BigDecimal value) {
            addCriterion("hair_price >=", value, "hairPrice");
            return (Criteria) this;
        }

        public Criteria andHairPriceLessThan(BigDecimal value) {
            addCriterion("hair_price <", value, "hairPrice");
            return (Criteria) this;
        }

        public Criteria andHairPriceLessThanOrEqualTo(BigDecimal value) {
            addCriterion("hair_price <=", value, "hairPrice");
            return (Criteria) this;
        }

        public Criteria andHairPriceLike(BigDecimal value) {
            addCriterion("hair_price like", value, "hairPrice");
            return (Criteria) this;
        }

        public Criteria andHairPriceNotLike(BigDecimal value) {
            addCriterion("hair_price not like", value, "hairPrice");
            return (Criteria) this;
        }

        public Criteria andHairPriceIn(List<BigDecimal> values) {
            addCriterion("hair_price in", values, "hairPrice");
            return (Criteria) this;
        }

        public Criteria andHairPriceNotIn(List<BigDecimal> values) {
            addCriterion("hair_price not in", values, "hairPrice");
            return (Criteria) this;
        }

        public Criteria andHairPriceBetween(BigDecimal value1, BigDecimal value2) {
            addCriterion("hair_price between", value1, value2, "hairPrice");
            return (Criteria) this;
        }

        public Criteria andHairPriceNotBetween(BigDecimal value1, BigDecimal value2) {
            addCriterion("hair_price not between", value1, value2, "hairPrice");
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


        public Criteria andHairDescIsNull() {
            addCriterion("hair_desc is null");
            return (Criteria) this;
        }

        public Criteria andHairDescIsNotNull() {
            addCriterion("hair_desc is not null");
            return (Criteria) this;
        }

        public Criteria andHairDescEqualTo(String value) {
            addCriterion("hair_desc =", value, "hairDesc");
            return (Criteria) this;
        }

        public Criteria andHairDescNotEqualTo(String value) {
            addCriterion("hair_desc <>", value, "hairDesc");
            return (Criteria) this;
        }

        public Criteria andHairDescGreaterThan(String value) {
            addCriterion("hair_desc >", value, "hairDesc");
            return (Criteria) this;
        }

        public Criteria andHairDescGreaterThanOrEqualTo(String value) {
            addCriterion("hair_desc >=", value, "hairDesc");
            return (Criteria) this;
        }

        public Criteria andHairDescLessThan(String value) {
            addCriterion("hair_desc <", value, "hairDesc");
            return (Criteria) this;
        }

        public Criteria andHairDescLessThanOrEqualTo(String value) {
            addCriterion("hair_desc <=", value, "hairDesc");
            return (Criteria) this;
        }

        public Criteria andHairDescLike(String value) {
            addCriterion("hair_desc like", value, "hairDesc");
            return (Criteria) this;
        }

        public Criteria andHairDescNotLike(String value) {
            addCriterion("hair_desc not like", value, "hairDesc");
            return (Criteria) this;
        }

        public Criteria andHairDescIn(List<String> values) {
            addCriterion("hair_desc in", values, "hairDesc");
            return (Criteria) this;
        }

        public Criteria andHairDescNotIn(List<String> values) {
            addCriterion("hair_desc not in", values, "hairDesc");
            return (Criteria) this;
        }

        public Criteria andHairDescBetween(String value1, String value2) {
            addCriterion("hair_desc between", value1, value2, "hairDesc");
            return (Criteria) this;
        }

        public Criteria andHairDescNotBetween(String value1, String value2) {
            addCriterion("hair_desc not between", value1, value2, "hairDesc");
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
