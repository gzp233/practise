package com.cpdms.service.dinning;

import java.util.List;
import java.util.Arrays;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;
import com.github.pagehelper.PageInfo;
import com.cpdms.common.base.BaseService;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.dinning.BaseEmpMapper;
import com.cpdms.model.dinning.BaseEmp;
import com.cpdms.model.dinning.BaseEmpExample;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.util.SnowflakeIdWorker;

/**
 * 员工信息，云闪付给数据 BaseEmpService
 * @Title: BaseEmpService.java 
 * @Package com.cpdms.dinning.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-10-24 16:51:18  
 **/
@Service
public class BaseEmpService implements BaseService<BaseEmp, BaseEmpExample>{
	@Autowired
	private BaseEmpMapper baseEmpMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<BaseEmp> list(Tablepar tablepar,String name){
	        BaseEmpExample testExample=new BaseEmpExample();
	        testExample.setOrderByClause("create_date DESC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andEmpNameEqualTo("%"+name+"%");
	        }
	        testExample.createCriteria().andStatusEqualTo(1);

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BaseEmp> list= baseEmpMapper.selectByExample(testExample);
	        PageInfo<BaseEmp> pageInfo = new PageInfo<BaseEmp>(list);
	        return  pageInfo;
	 }

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista=Convert.toListStrArray(ids);
			BaseEmpExample example=new BaseEmpExample();
			example.createCriteria().andIdIn(lista);
			return baseEmpMapper.deleteByExample(example);


	}


	@Override
	public BaseEmp selectByPrimaryKey(String id) {

			return baseEmpMapper.selectByPrimaryKey(id);

	}

	public BaseEmp selectByOpenId(String openId) {
	     return baseEmpMapper.selectByOpenId(openId);
    }

	public BaseEmp selectByCardNo(String cardNo) {
	     return baseEmpMapper.selectByCardNo(cardNo);
    }

	public BaseEmp selectByEncryptCardNo(String encryptCardNo) {
	     return baseEmpMapper.selectByEncryptCardNo(encryptCardNo);
    }


	@Override
	public int updateByPrimaryKeySelective(BaseEmp record) {
	     BaseEmpExample baseEmpExample = new BaseEmpExample();
			baseEmpExample.createCriteria().andStatusEqualTo(1).andCardNoEqualTo(record.getCardNo());
			List<BaseEmp> baseEmpList = baseEmpMapper.selectByExample(baseEmpExample);
			if (baseEmpList.size() > 1) {
			    return 0;
            }
            if (baseEmpList.size() == 1) {
                if (!record.getId().equals(baseEmpList.get(0).getId())) {
                    return 0;
                }
            }
		return baseEmpMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(BaseEmp record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());
			BaseEmpExample baseEmpExample = new BaseEmpExample();
			baseEmpExample.createCriteria().andStatusEqualTo(1).andCardNoEqualTo(record.getCardNo());
			List<BaseEmp> baseEmpList = baseEmpMapper.selectByExample(baseEmpExample);
			if (baseEmpList.size() > 0) {
			    return 0;
            }


		return baseEmpMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(BaseEmp record, BaseEmpExample example) {

		return baseEmpMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(BaseEmp record, BaseEmpExample example) {

		return baseEmpMapper.updateByExample(record, example);
	}

	@Override
	public List<BaseEmp> selectByExample(BaseEmpExample example) {

		return baseEmpMapper.selectByExample(example);
	}


	@Override
	public long countByExample(BaseEmpExample example) {

		return baseEmpMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(BaseEmpExample example) {

		return baseEmpMapper.deleteByExample(example);
	}

}
