package com.cpdms.service.personnel;

import java.io.File;
import java.util.*;

import cn.hutool.core.io.FileUtil;
import cn.hutool.core.math.MathUtil;
import cn.hutool.core.util.RandomUtil;
import cn.hutool.json.JSONObject;
import cn.hutool.poi.excel.ExcelReader;
import cn.hutool.poi.excel.ExcelUtil;
import com.cpdms.common.base.BaseService;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.conf.UnionpayConfig;
import com.cpdms.common.conf.V2Config;
import com.cpdms.common.support.Convert;
import com.cpdms.controller.dinning.UnionpayController;
import com.cpdms.mapper.personnel.BasePersonnelMapper;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.personnel.BasePersonnel;
import com.cpdms.model.personnel.BasePersonnelExample;
import com.cpdms.util.*;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.scheduling.annotation.Async;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;
import org.springframework.transaction.annotation.Transactional;

/**
 * 行员信息 BasePersonnelService
 * @Title: BasePersonnelService.java 
 * @Package com.cpdms.personnel.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2020-01-11 10:42:23  
 **/
@Service
public class BasePersonnelService implements BaseService<BasePersonnel, BasePersonnelExample> {
	@Autowired
	private BasePersonnelMapper basePersonnelMapper;
	@Autowired
	private AsyncService asyncService;

	/**
	 * 分页查询
	 * @param
	 * @param
	 * @return
	 */
	 public PageInfo<BasePersonnel> list(Tablepar tablepar, BasePersonnel basePersonnel){
	        BasePersonnelExample testExample=new BasePersonnelExample();
		 testExample.setOrderByClause("update_date DESC");
		 BasePersonnelExample.Criteria criteria = testExample.createCriteria();

		 criteria.andStatusEqualTo(1);
		if(basePersonnel.getPersonnelNo()!=null&&!"".equals(basePersonnel.getPersonnelNo())){
			criteria.andPersonnelNoLike("%"+basePersonnel.getPersonnelNo()+"%");
		}
		 if(basePersonnel.getPersonnelName()!=null&&!"".equals(basePersonnel.getPersonnelName())){
			 criteria.andPersonnelNameLike("%"+basePersonnel.getPersonnelName()+"%");
		 }
		 if(basePersonnel.getPersonnelMobile()!=null&&!"".equals(basePersonnel.getPersonnelMobile())){
			 criteria.andPersonnelMobileLike("%"+basePersonnel.getPersonnelMobile()+"%");
		 }
		 if(basePersonnel.getDeptName()!=null&&!"".equals(basePersonnel.getDeptName())){
			 criteria.andDeptNameLike("%"+basePersonnel.getDeptName()+"%");
		 }
		 if(basePersonnel.getCardNo()!=null&&!"".equals(basePersonnel.getCardNo())){
			 criteria.andCardNoLike("%"+basePersonnel.getCardNo()+"%");
		 }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BasePersonnel> list= basePersonnelMapper.selectByExample(testExample);
	        PageInfo<BasePersonnel> pageInfo = new PageInfo<>(list);
	        return  pageInfo;
	 }

	 public boolean syncById(String id) {
	 	BasePersonnel basePersonnel = basePersonnelMapper.selectByPrimaryKey(id);
	 	if (basePersonnel == null) {
	 		return false;
		}
		 JSONObject result = asyncService.syncPersonnel(basePersonnel, AsyncService.TRXTP_MODIFY);
		 if (result.get("code") != null) {
			 basePersonnel.setRespCode(result.getStr("code"));
			 basePersonnel.setRespMsg(result.getStr("msg"));
			 if (result.getStr("code").equals("1000")) {
				 basePersonnel.setSyncStatus(1);
			 } else {
				 basePersonnel.setSyncStatus(0);
			 }
			 basePersonnelMapper.updateByPrimaryKey(basePersonnel);
		 }

	 	return true;
	 }

	@Override
	public int deleteByPrimaryKey(String ids) {
				
			List<String> lista= Convert.toListStrArray(ids);
			List<String> closeOk = new ArrayList<>();
			// 先调用接口删除
			for (String id : lista) {
				BasePersonnel exist = basePersonnelMapper.selectByPrimaryKey(id);
				if (exist.getSyncStatus() == 1) {
					JSONObject result = asyncService.userClose(exist);
					if (result.get("code") != null && result.getStr("code").equals("1000")) {
						closeOk.add(id);
					}
				} else {
					closeOk.add(id);
				}
			}
			BasePersonnelExample example=new BasePersonnelExample();
			example.createCriteria().andIdIn(closeOk);
			BasePersonnel basePersonnel = new BasePersonnel();
			basePersonnel.setStatus(0);
			return basePersonnelMapper.updateByExampleSelective(basePersonnel, example);
	}

	@Override
	public BasePersonnel selectByPrimaryKey(String id) {
				
			return basePersonnelMapper.selectByPrimaryKey(id);
				
	}

	
	@Override
	public int updateByPrimaryKeySelective(BasePersonnel record) {
		return basePersonnelMapper.updateByPrimaryKeySelective(record);
	}

	public String updateByPrimaryKeySelective2(BasePersonnel record) {
		BasePersonnel basePersonnel = basePersonnelMapper.selectByPrimaryKey(record.getId());
		if (StringUtils.isNotEmpty(record.getPhotoName())) {
			// 重新计算
			String calcValue = ImgCalcUtils.calcByGo(record.getPhotoName());
			if (calcValue.equals("")) {
				record.setCalcResult(2);
			} else {
				record.setCalcResult(1);
				record.setCalcValue(calcValue);
			}
		}
		if (!basePersonnel.getCardNo().equals(record.getCardNo()) || StringUtils.isNotEmpty(record.getPhotoName())) {
			if (record.getCalcValue() == null) {
				record.setCalcValue(basePersonnel.getCalcValue());
			}
			JSONObject result = asyncService.syncPersonnel(record, AsyncService.TRXTP_MODIFY);
			if (result.get("code") != null) {
				record.setRespCode(result.getStr("code"));
				record.setRespMsg(result.getStr("msg"));
				if (result.getStr("code").equals("1000")) {
					record.setSyncStatus(1);
				}
			}
		}
		int b =  basePersonnelMapper.updateByPrimaryKeySelective(record);
		if (b<=0) {
			return "修改失败";
		}

		return "";
	}
	
	
	/**
	 * 添加
	 */
	@Override
	public int insertSelective(BasePersonnel record) {
					//添加雪花主键id
		record.setId(SnowflakeIdWorker.getUUID());

		return basePersonnelMapper.insertSelective(record);
	}

	public String insertSelective2(BasePersonnel record) {
		// 检查是否重复
		List<BasePersonnel> basePersonnelList = basePersonnelMapper.selectExists(record.getCardNo(),record.getPersonnelNo(), record.getPersonnelMobile());
		if (basePersonnelList.size() > 0) {
			BasePersonnel exist = basePersonnelList.get(0);
			if (exist.getPersonnelNo().equals(record.getPersonnelNo())) {
				return "员工编号重复";
			}
			if (exist.getCardNo().equals(record.getCardNo())) {
				return "饭卡号重复";
			}
			if (exist.getPersonnelMobile().equals(record.getPersonnelMobile())) {
				return "手机号重复";
			}
		}
		//添加雪花主键id
		record.setId(SnowflakeIdWorker.getUUID());
		// 计算特征值
		String calcValue = ImgCalcUtils.calcByGo(record.getPhotoName());
		if (calcValue.equals("")) {
			record.setCalcResult(2);
		} else {
			record.setCalcResult(1);
			record.setCalcValue(calcValue);
		}
		JSONObject result = asyncService.syncPersonnel(record, AsyncService.TRXTP_ADD);
		if (result.get("code") != null) {
			record.setRespCode(result.getStr("code"));
			record.setRespMsg(result.getStr("msg"));
			if (result.getStr("code").equals("1000")) {
				record.setSyncStatus(1);
			}
		}

		int b = basePersonnelMapper.insertSelective(record);
		if (b <= 0) {
			return "添加失败";
		}
		return "";
	}
	
	
	@Override
	public int updateByExampleSelective(BasePersonnel record, BasePersonnelExample example) {
		
		return basePersonnelMapper.updateByExampleSelective(record, example);
	}

	
	@Override
	public int updateByExample(BasePersonnel record, BasePersonnelExample example) {
		
		return basePersonnelMapper.updateByExample(record, example);
	}

	@Override
	public List<BasePersonnel> selectByExample(BasePersonnelExample example) {
		
		return basePersonnelMapper.selectByExample(example);
	}

	
	@Override
	public long countByExample(BasePersonnelExample example) {
		
		return basePersonnelMapper.countByExample(example);
	}

	
	@Override
	public int deleteByExample(BasePersonnelExample example) {
		return basePersonnelMapper.deleteByExample(example);
	}
	
	/**
	 * 检查name
	 * @param basePersonnel
	 * @return
	 */
	public int checkNameUnique(BasePersonnel basePersonnel){
		BasePersonnelExample example=new BasePersonnelExample();
		example.createCriteria().andPersonnelNoEqualTo(basePersonnel.getPersonnelNo());
		List<BasePersonnel> list=basePersonnelMapper.selectByExample(example);
		return list.size();
	}


}
