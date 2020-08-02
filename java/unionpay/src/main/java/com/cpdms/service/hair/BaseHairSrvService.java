package com.cpdms.service.hair;

import java.util.List;

import com.cpdms.common.base.BaseService;
import com.cpdms.common.base.PageInfo;
import com.cpdms.common.support.Convert;
import com.cpdms.mapper.hair.BaseHairSrvMapper;
import com.cpdms.model.custom.Tablepar;
import com.cpdms.model.hair.BaseHairSrv;
import com.cpdms.model.hair.BaseHairSrvExample;
import com.cpdms.util.SnowflakeIdWorker;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import com.github.pagehelper.PageHelper;

/**
 * 服务类型表 BaseHairSrvService
 * @Title: BaseHairSrvService.java 
 * @Package com.cpdms.hair.service 
 * @author Eric_自动生成
 * @email eric@gmail.com
 * @date 2019-11-14 15:26:29  
 **/
@Service
public class BaseHairSrvService implements BaseService<BaseHairSrv, BaseHairSrvExample> {
	@Autowired
	private BaseHairSrvMapper baseHairSrvMapper;


	/**
	 * 分页查询
	 * @param pageNum
	 * @param pageSize
	 * @return
	 */
	 public PageInfo<BaseHairSrv> list(Tablepar tablepar, String name){
	        BaseHairSrvExample testExample=new BaseHairSrvExample();
	        testExample.setOrderByClause("id ASC");
	        if(name!=null&&!"".equals(name)){
	        	testExample.createCriteria().andServiceNameLike("%"+name+"%");
	        }

	        PageHelper.startPage(tablepar.getPageNum(), tablepar.getPageSize());
	        List<BaseHairSrv> list= baseHairSrvMapper.selectByExample(testExample);
	        PageInfo<BaseHairSrv> pageInfo = new PageInfo<BaseHairSrv>(list);
	        return  pageInfo;
	 }

	 public List<BaseHairSrv> all() {
            BaseHairSrvExample testExample=new BaseHairSrvExample();
            testExample.createCriteria().andStatusEqualTo(1);
			return baseHairSrvMapper.selectByExample(testExample);


	}

	@Override
	public int deleteByPrimaryKey(String ids) {

			List<String> lista= Convert.toListStrArray(ids);
			BaseHairSrvExample example=new BaseHairSrvExample();
			example.createCriteria().andIdIn(lista);
			BaseHairSrv baseHairSrv = new BaseHairSrv();
			baseHairSrv.setStatus(0);
			return baseHairSrvMapper.updateByExampleSelective(baseHairSrv, example);


	}


	@Override
	public BaseHairSrv selectByPrimaryKey(String id) {

			return baseHairSrvMapper.selectByPrimaryKey(id);

	}


	@Override
	public int updateByPrimaryKeySelective(BaseHairSrv record) {
		return baseHairSrvMapper.updateByPrimaryKeySelective(record);
	}


	/**
	 * 添加
	 */
	@Override
	public int insertSelective(BaseHairSrv record) {
					//添加雪花主键id
			record.setId(SnowflakeIdWorker.getUUID());


		return baseHairSrvMapper.insertSelective(record);
	}


	@Override
	public int updateByExampleSelective(BaseHairSrv record, BaseHairSrvExample example) {

		return baseHairSrvMapper.updateByExampleSelective(record, example);
	}


	@Override
	public int updateByExample(BaseHairSrv record, BaseHairSrvExample example) {

		return baseHairSrvMapper.updateByExample(record, example);
	}

	@Override
	public List<BaseHairSrv> selectByExample(BaseHairSrvExample example) {

		return baseHairSrvMapper.selectByExample(example);
	}


	@Override
	public long countByExample(BaseHairSrvExample example) {

		return baseHairSrvMapper.countByExample(example);
	}


	@Override
	public int deleteByExample(BaseHairSrvExample example) {

		return baseHairSrvMapper.deleteByExample(example);
	}

	/**
	 * 检查name
	 * @param baseHairSrv
	 * @return
	 */
	public int checkNameUnique(BaseHairSrv baseHairSrv){
		BaseHairSrvExample example=new BaseHairSrvExample();
		example.createCriteria().andServiceNameEqualTo(baseHairSrv.getServiceName());
		List<BaseHairSrv> list=baseHairSrvMapper.selectByExample(example);
		return list.size();
	}


}
