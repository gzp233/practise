package com.cpdms.model.dinning;

import java.math.BigDecimal;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

/**
 * 预定订单 BizOrderExample
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:42:45
 */
public class BizOrderExample {

    protected String orderByClause;

    protected boolean distinct;

    protected List<Criteria> oredCriteria;

    public BizOrderExample() {
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


        public Criteria andOrdCodeIsNull() {
            addCriterion("ord_code is null");
            return (Criteria) this;
        }

        public Criteria andOrdCodeIsNotNull() {
            addCriterion("ord_code is not null");
            return (Criteria) this;
        }

        public Criteria andOrdCodeEqualTo(String value) {
            addCriterion("ord_code =", value, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeNotEqualTo(String value) {
            addCriterion("ord_code <>", value, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeGreaterThan(String value) {
            addCriterion("ord_code >", value, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeGreaterThanOrEqualTo(String value) {
            addCriterion("ord_code >=", value, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeLessThan(String value) {
            addCriterion("ord_code <", value, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeLessThanOrEqualTo(String value) {
            addCriterion("ord_code <=", value, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeLike(String value) {
            addCriterion("ord_code like", value, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeNotLike(String value) {
            addCriterion("ord_code not like", value, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeIn(List<String> values) {
            addCriterion("ord_code in", values, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeNotIn(List<String> values) {
            addCriterion("ord_code not in", values, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeBetween(String value1, String value2) {
            addCriterion("ord_code between", value1, value2, "ordCode");
            return (Criteria) this;
        }

        public Criteria andOrdCodeNotBetween(String value1, String value2) {
            addCriterion("ord_code not between", value1, value2, "ordCode");
            return (Criteria) this;
        }


        public Criteria andOrdTimeIsNull() {
            addCriterion("ord_time is null");
            return (Criteria) this;
        }

        public Criteria andOrdTimeIsNotNull() {
            addCriterion("ord_time is not null");
            return (Criteria) this;
        }

        public Criteria andOrdTimeEqualTo(Date value) {
            addCriterion("ord_time =", value, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeNotEqualTo(Date value) {
            addCriterion("ord_time <>", value, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeGreaterThan(Date value) {
            addCriterion("ord_time >", value, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeGreaterThanOrEqualTo(Date value) {
            addCriterion("ord_time >=", value, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeLessThan(Date value) {
            addCriterion("ord_time <", value, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeLessThanOrEqualTo(Date value) {
            addCriterion("ord_time <=", value, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeLike(Date value) {
            addCriterion("ord_time like", value, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeNotLike(Date value) {
            addCriterion("ord_time not like", value, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeIn(List<Date> values) {
            addCriterion("ord_time in", values, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeNotIn(List<Date> values) {
            addCriterion("ord_time not in", values, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeBetween(Date value1, Date value2) {
            addCriterion("ord_time between", value1, value2, "ordTime");
            return (Criteria) this;
        }

        public Criteria andOrdTimeNotBetween(Date value1, Date value2) {
            addCriterion("ord_time not between", value1, value2, "ordTime");
            return (Criteria) this;
        }


        public Criteria andOrdEmpIdIsNull() {
            addCriterion("ord_emp_id is null");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdIsNotNull() {
            addCriterion("ord_emp_id is not null");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdEqualTo(String value) {
            addCriterion("ord_emp_id =", value, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andSellTypeNameEqualTo(String value) {
            addCriterion("sell_type_name =", value, "sellTypeName");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdNotEqualTo(String value) {
            addCriterion("ord_emp_id <>", value, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdGreaterThan(String value) {
            addCriterion("ord_emp_id >", value, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdGreaterThanOrEqualTo(String value) {
            addCriterion("ord_emp_id >=", value, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdLessThan(String value) {
            addCriterion("ord_emp_id <", value, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdLessThanOrEqualTo(String value) {
            addCriterion("ord_emp_id <=", value, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdLike(String value) {
            addCriterion("ord_emp_id like", value, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdNotLike(String value) {
            addCriterion("ord_emp_id not like", value, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdIn(List<String> values) {
            addCriterion("ord_emp_id in", values, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdNotIn(List<String> values) {
            addCriterion("ord_emp_id not in", values, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdBetween(String value1, String value2) {
            addCriterion("ord_emp_id between", value1, value2, "ordEmpId");
            return (Criteria) this;
        }

        public Criteria andOrdEmpIdNotBetween(String value1, String value2) {
            addCriterion("ord_emp_id not between", value1, value2, "ordEmpId");
            return (Criteria) this;
        }


        public Criteria andOrdTimeRangeIdIsNull() {
            addCriterion("ord_time_range_id is null");
            return (Criteria) this;
        }

        public Criteria andOrdTimeRangeIdIsNotNull() {
            addCriterion("ord_time_range_id is not null");
            return (Criteria) this;
        }

        public Criteria andOrdTimeRangeIdEqualTo(String value) {
            addCriterion("ord_time_range_id =", value, "ordTimeRangeId");
            return (Criteria) this;
        }

        public Criteria andOrdTimeRangeIdNotEqualTo(String value) {
            addCriterion("ord_time_range_id <>", value, "ordTimeRangeId");
            return (Criteria) this;
        }

        public Criteria andOrdTimeRangeIdGreaterThan(String value) {
            addCriterion("ord_time_range_id >", value, "ordTimeRangeId");
            return (Criteria) this;
        }

        public Criteria andOrdTimeRangeIdGreaterThanOrEqualTo(String value) {
            addCriterion("ord_time_range_id >=", value, "ordTimeRangeId");
            return (Criteria) this;
        }

        public Criteria andOrdTimeRangeIdLessThan(String value) {
            addCriterion("ord_time_range_id <", value, "ordTimeRangeId");
            return (Criteria) this;
        }

        public Criteria andOrdTimeRangeIdLessThanOrEqualTo(String value) {
            addCriterion("ord_time_range_id <=", value, "ordTimeRangeId");
            return (Criteria) this;
        }

        public Criteria andOrdTimeRangeIdLike(String value) {
            addCriterion("ord_time_range_id like", value, "ordTimeRangeId");
            return (Criteria) this;
        }

        public Criteria andOrdTimeRangeIdNotLike(String value) {
            addCriterion("ord_time_range_id not like", value, "ordTimeRangeId");
            return (Criteria) this;
        }

        public Criteria andOrdTimeRangeIdIn(List<String> values) {
            addCriterion("ord_time_range_id in", values, "ordTimeRangeId");
            return (Criteria) this;
        }

        public Criteria andOrdTimeRangeIdNotIn(List<String> values) {
            addCriterion("ord_time_range_id not in", values, "ordTimeRangeId");
            return (Criteria) this;
        }

        public Criteria andOrdTimeRangeIdBetween(String value1, String value2) {
            addCriterion("ord_time_range_id between", value1, value2, "ordTimeRangeId");
            return (Criteria) this;
        }

        public Criteria andOrdTimeRangeIdNotBetween(String value1, String value2) {
            addCriterion("ord_time_range_id not between", value1, value2, "ordTimeRangeId");
            return (Criteria) this;
        }


        public Criteria andOrdStateIsNull() {
            addCriterion("ord_state is null");
            return (Criteria) this;
        }

        public Criteria andOrdStateIsNotNull() {
            addCriterion("ord_state is not null");
            return (Criteria) this;
        }

        public Criteria andOrdStateEqualTo(String value) {
            addCriterion("ord_state =", value, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateNotEqualTo(String value) {
            addCriterion("ord_state <>", value, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateGreaterThan(String value) {
            addCriterion("ord_state >", value, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateGreaterThanOrEqualTo(String value) {
            addCriterion("ord_state >=", value, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateLessThan(String value) {
            addCriterion("ord_state <", value, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateLessThanOrEqualTo(String value) {
            addCriterion("ord_state <=", value, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateLike(String value) {
            addCriterion("ord_state like", value, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateNotLike(String value) {
            addCriterion("ord_state not like", value, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateIn(List<String> values) {
            addCriterion("ord_state in", values, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateNotIn(List<String> values) {
            addCriterion("ord_state not in", values, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateBetween(String value1, String value2) {
            addCriterion("ord_state between", value1, value2, "ordState");
            return (Criteria) this;
        }

        public Criteria andOrdStateNotBetween(String value1, String value2) {
            addCriterion("ord_state not between", value1, value2, "ordState");
            return (Criteria) this;
        }


        public Criteria andOrdAmtIsNull() {
            addCriterion("ord_amt is null");
            return (Criteria) this;
        }

        public Criteria andOrdAmtIsNotNull() {
            addCriterion("ord_amt is not null");
            return (Criteria) this;
        }

        public Criteria andOrdAmtEqualTo(BigDecimal value) {
            addCriterion("ord_amt =", value, "ordAmt");
            return (Criteria) this;
        }

        public Criteria andOrdAmtNotEqualTo(BigDecimal value) {
            addCriterion("ord_amt <>", value, "ordAmt");
            return (Criteria) this;
        }

        public Criteria andOrdAmtGreaterThan(BigDecimal value) {
            addCriterion("ord_amt >", value, "ordAmt");
            return (Criteria) this;
        }

        public Criteria andOrdAmtGreaterThanOrEqualTo(BigDecimal value) {
            addCriterion("ord_amt >=", value, "ordAmt");
            return (Criteria) this;
        }

        public Criteria andOrdAmtLessThan(BigDecimal value) {
            addCriterion("ord_amt <", value, "ordAmt");
            return (Criteria) this;
        }

        public Criteria andOrdAmtLessThanOrEqualTo(BigDecimal value) {
            addCriterion("ord_amt <=", value, "ordAmt");
            return (Criteria) this;
        }

        public Criteria andOrdAmtLike(BigDecimal value) {
            addCriterion("ord_amt like", value, "ordAmt");
            return (Criteria) this;
        }

        public Criteria andOrdAmtNotLike(BigDecimal value) {
            addCriterion("ord_amt not like", value, "ordAmt");
            return (Criteria) this;
        }

        public Criteria andOrdAmtIn(List<BigDecimal> values) {
            addCriterion("ord_amt in", values, "ordAmt");
            return (Criteria) this;
        }

        public Criteria andOrdAmtNotIn(List<BigDecimal> values) {
            addCriterion("ord_amt not in", values, "ordAmt");
            return (Criteria) this;
        }

        public Criteria andOrdAmtBetween(BigDecimal value1, BigDecimal value2) {
            addCriterion("ord_amt between", value1, value2, "ordAmt");
            return (Criteria) this;
        }

        public Criteria andOrdAmtNotBetween(BigDecimal value1, BigDecimal value2) {
            addCriterion("ord_amt not between", value1, value2, "ordAmt");
            return (Criteria) this;
        }


        public Criteria andOrdSrcIsNull() {
            addCriterion("ord_src is null");
            return (Criteria) this;
        }

        public Criteria andOrdSrcIsNotNull() {
            addCriterion("ord_src is not null");
            return (Criteria) this;
        }

        public Criteria andOrdSrcEqualTo(String value) {
            addCriterion("ord_src =", value, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcNotEqualTo(String value) {
            addCriterion("ord_src <>", value, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcGreaterThan(String value) {
            addCriterion("ord_src >", value, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcGreaterThanOrEqualTo(String value) {
            addCriterion("ord_src >=", value, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcLessThan(String value) {
            addCriterion("ord_src <", value, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcLessThanOrEqualTo(String value) {
            addCriterion("ord_src <=", value, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcLike(String value) {
            addCriterion("ord_src like", value, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcNotLike(String value) {
            addCriterion("ord_src not like", value, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcIn(List<String> values) {
            addCriterion("ord_src in", values, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcNotIn(List<String> values) {
            addCriterion("ord_src not in", values, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcBetween(String value1, String value2) {
            addCriterion("ord_src between", value1, value2, "ordSrc");
            return (Criteria) this;
        }

        public Criteria andOrdSrcNotBetween(String value1, String value2) {
            addCriterion("ord_src not between", value1, value2, "ordSrc");
            return (Criteria) this;
        }


        public Criteria andTakeByIsNull() {
            addCriterion("take_by is null");
            return (Criteria) this;
        }

        public Criteria andTakeByIsNotNull() {
            addCriterion("take_by is not null");
            return (Criteria) this;
        }

        public Criteria andTakeByEqualTo(String value) {
            addCriterion("take_by =", value, "takeBy");
            return (Criteria) this;
        }

        public Criteria andTakeByNotEqualTo(String value) {
            addCriterion("take_by <>", value, "takeBy");
            return (Criteria) this;
        }

        public Criteria andTakeByGreaterThan(String value) {
            addCriterion("take_by >", value, "takeBy");
            return (Criteria) this;
        }

        public Criteria andTakeByGreaterThanOrEqualTo(String value) {
            addCriterion("take_by >=", value, "takeBy");
            return (Criteria) this;
        }

        public Criteria andTakeByLessThan(String value) {
            addCriterion("take_by <", value, "takeBy");
            return (Criteria) this;
        }

        public Criteria andTakeByLessThanOrEqualTo(String value) {
            addCriterion("take_by <=", value, "takeBy");
            return (Criteria) this;
        }

        public Criteria andTakeByLike(String value) {
            addCriterion("take_by like", value, "takeBy");
            return (Criteria) this;
        }

        public Criteria andTakeByNotLike(String value) {
            addCriterion("take_by not like", value, "takeBy");
            return (Criteria) this;
        }

        public Criteria andTakeByIn(List<String> values) {
            addCriterion("take_by in", values, "takeBy");
            return (Criteria) this;
        }

        public Criteria andTakeByNotIn(List<String> values) {
            addCriterion("take_by not in", values, "takeBy");
            return (Criteria) this;
        }

        public Criteria andTakeByBetween(String value1, String value2) {
            addCriterion("take_by between", value1, value2, "takeBy");
            return (Criteria) this;
        }

        public Criteria andTakeByNotBetween(String value1, String value2) {
            addCriterion("take_by not between", value1, value2, "takeBy");
            return (Criteria) this;
        }


        public Criteria andTelIsNull() {
            addCriterion("tel is null");
            return (Criteria) this;
        }

        public Criteria andTelIsNotNull() {
            addCriterion("tel is not null");
            return (Criteria) this;
        }

        public Criteria andTelEqualTo(String value) {
            addCriterion("tel =", value, "tel");
            return (Criteria) this;
        }

        public Criteria andTelNotEqualTo(String value) {
            addCriterion("tel <>", value, "tel");
            return (Criteria) this;
        }

        public Criteria andTelGreaterThan(String value) {
            addCriterion("tel >", value, "tel");
            return (Criteria) this;
        }

        public Criteria andTelGreaterThanOrEqualTo(String value) {
            addCriterion("tel >=", value, "tel");
            return (Criteria) this;
        }

        public Criteria andTelLessThan(String value) {
            addCriterion("tel <", value, "tel");
            return (Criteria) this;
        }

        public Criteria andTelLessThanOrEqualTo(String value) {
            addCriterion("tel <=", value, "tel");
            return (Criteria) this;
        }

        public Criteria andTelLike(String value) {
            addCriterion("tel like", value, "tel");
            return (Criteria) this;
        }

        public Criteria andTelNotLike(String value) {
            addCriterion("tel not like", value, "tel");
            return (Criteria) this;
        }

        public Criteria andTelIn(List<String> values) {
            addCriterion("tel in", values, "tel");
            return (Criteria) this;
        }

        public Criteria andTelNotIn(List<String> values) {
            addCriterion("tel not in", values, "tel");
            return (Criteria) this;
        }

        public Criteria andTelBetween(String value1, String value2) {
            addCriterion("tel between", value1, value2, "tel");
            return (Criteria) this;
        }

        public Criteria andTelNotBetween(String value1, String value2) {
            addCriterion("tel not between", value1, value2, "tel");
            return (Criteria) this;
        }


        public Criteria andOrdMemoIsNull() {
            addCriterion("ord_memo is null");
            return (Criteria) this;
        }

        public Criteria andOrdMemoIsNotNull() {
            addCriterion("ord_memo is not null");
            return (Criteria) this;
        }

        public Criteria andOrdMemoEqualTo(String value) {
            addCriterion("ord_memo =", value, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoNotEqualTo(String value) {
            addCriterion("ord_memo <>", value, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoGreaterThan(String value) {
            addCriterion("ord_memo >", value, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoGreaterThanOrEqualTo(String value) {
            addCriterion("ord_memo >=", value, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoLessThan(String value) {
            addCriterion("ord_memo <", value, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoLessThanOrEqualTo(String value) {
            addCriterion("ord_memo <=", value, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoLike(String value) {
            addCriterion("ord_memo like", value, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoNotLike(String value) {
            addCriterion("ord_memo not like", value, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoIn(List<String> values) {
            addCriterion("ord_memo in", values, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoNotIn(List<String> values) {
            addCriterion("ord_memo not in", values, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoBetween(String value1, String value2) {
            addCriterion("ord_memo between", value1, value2, "ordMemo");
            return (Criteria) this;
        }

        public Criteria andOrdMemoNotBetween(String value1, String value2) {
            addCriterion("ord_memo not between", value1, value2, "ordMemo");
            return (Criteria) this;
        }


        public Criteria andPlanedTakeTimeIsNull() {
            addCriterion("planed_take_time is null");
            return (Criteria) this;
        }

        public Criteria andPlanedTakeTimeIsNotNull() {
            addCriterion("planed_take_time is not null");
            return (Criteria) this;
        }

        public Criteria andPlanedTakeTimeEqualTo(String value) {
            addCriterion("planed_take_time =", value, "planedTakeTime");
            return (Criteria) this;
        }

        public Criteria andPlanedTakeTimeNotEqualTo(String value) {
            addCriterion("planed_take_time <>", value, "planedTakeTime");
            return (Criteria) this;
        }

        public Criteria andPlanedTakeTimeGreaterThan(String value) {
            addCriterion("planed_take_time >", value, "planedTakeTime");
            return (Criteria) this;
        }

        public Criteria andPlanedTakeTimeGreaterThanOrEqualTo(String value) {
            addCriterion("planed_take_time >=", value, "planedTakeTime");
            return (Criteria) this;
        }

        public Criteria andPlanedTakeTimeLessThan(String value) {
            addCriterion("planed_take_time <", value, "planedTakeTime");
            return (Criteria) this;
        }

        public Criteria andPlanedTakeTimeLessThanOrEqualTo(String value) {
            addCriterion("planed_take_time <=", value, "planedTakeTime");
            return (Criteria) this;
        }

        public Criteria andPlanedTakeTimeLike(String value) {
            addCriterion("planed_take_time like", value, "planedTakeTime");
            return (Criteria) this;
        }

        public Criteria andPlanedTakeTimeNotLike(String value) {
            addCriterion("planed_take_time not like", value, "planedTakeTime");
            return (Criteria) this;
        }

        public Criteria andPlanedTakeTimeIn(List<String> values) {
            addCriterion("planed_take_time in", values, "planedTakeTime");
            return (Criteria) this;
        }

        public Criteria andPlanedTakeTimeNotIn(List<String> values) {
            addCriterion("planed_take_time not in", values, "planedTakeTime");
            return (Criteria) this;
        }

        public Criteria andPlanedTakeTimeBetween(String value1, String value2) {
            addCriterion("planed_take_time between", value1, value2, "planedTakeTime");
            return (Criteria) this;
        }

        public Criteria andPlanedTakeTimeNotBetween(String value1, String value2) {
            addCriterion("planed_take_time not between", value1, value2, "planedTakeTime");
            return (Criteria) this;
        }


        public Criteria andActualTakeTimeIsNull() {
            addCriterion("actual_take_time is null");
            return (Criteria) this;
        }

        public Criteria andActualTakeTimeIsNotNull() {
            addCriterion("actual_take_time is not null");
            return (Criteria) this;
        }

        public Criteria andActualTakeTimeEqualTo(String value) {
            addCriterion("actual_take_time =", value, "actualTakeTime");
            return (Criteria) this;
        }

        public Criteria andActualTakeTimeNotEqualTo(String value) {
            addCriterion("actual_take_time <>", value, "actualTakeTime");
            return (Criteria) this;
        }

        public Criteria andActualTakeTimeGreaterThan(String value) {
            addCriterion("actual_take_time >", value, "actualTakeTime");
            return (Criteria) this;
        }

        public Criteria andActualTakeTimeGreaterThanOrEqualTo(String value) {
            addCriterion("actual_take_time >=", value, "actualTakeTime");
            return (Criteria) this;
        }

        public Criteria andActualTakeTimeLessThan(String value) {
            addCriterion("actual_take_time <", value, "actualTakeTime");
            return (Criteria) this;
        }

        public Criteria andActualTakeTimeLessThanOrEqualTo(String value) {
            addCriterion("actual_take_time <=", value, "actualTakeTime");
            return (Criteria) this;
        }

        public Criteria andActualTakeTimeLike(String value) {
            addCriterion("actual_take_time like", value, "actualTakeTime");
            return (Criteria) this;
        }

        public Criteria andActualTakeTimeNotLike(String value) {
            addCriterion("actual_take_time not like", value, "actualTakeTime");
            return (Criteria) this;
        }

        public Criteria andActualTakeTimeIn(List<String> values) {
            addCriterion("actual_take_time in", values, "actualTakeTime");
            return (Criteria) this;
        }

        public Criteria andActualTakeTimeNotIn(List<String> values) {
            addCriterion("actual_take_time not in", values, "actualTakeTime");
            return (Criteria) this;
        }

        public Criteria andActualTakeTimeBetween(String value1, String value2) {
            addCriterion("actual_take_time between", value1, value2, "actualTakeTime");
            return (Criteria) this;
        }

        public Criteria andActualTakeTimeNotBetween(String value1, String value2) {
            addCriterion("actual_take_time not between", value1, value2, "actualTakeTime");
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
